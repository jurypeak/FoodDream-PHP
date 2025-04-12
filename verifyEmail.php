<?php

require 'vendor/autoload.php';

use MailerSend\Exceptions\MailerSendException;
use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\MailerSend;

// Function to generate a random verification code
function generateVerificationCode($length): string
{
    $result = '';

    for ($i = 0; $i < $length; $i++) {
        $result .= random_int(0, 9);
    }

    return $result;
}

$response = array();

$jsondata = file_get_contents("php://input");
$data = json_decode($jsondata, true);
$email = $data['email'];
$code = generateVerificationCode(6);

// Send the email using the MailerSend API. https://developers.mailersend.com/

try {
    $mailersend = new MailerSend(['api_key' => 'mlsn.fa87a701fdbc3f433ed4f272abc928cce132773712598fba3b896c2e43f6767b']);

    $recipients = [
        new Recipient($email, 'Customer'),
    ];

    $personalization = [
        new Personalization($email, [
            'code' => $code,
            'support_email' => 'fooddreamsupport@jurypeak.com'
        ])
    ];

    $emailParams = (new EmailParams())
        ->setFrom('MS_kgqdqa@jurypeak.com')
        ->setFromName('Food Dream Support')
        ->setRecipients($recipients)
        ->setSubject('Email Verification')
        ->setTemplateId('yzkq3408vrkgd796')
        ->setPersonalization($personalization);

    $response = $mailersend->email->send($emailParams);
    $values["status"] = "Success";
    $values["message"] = "Email has been sent successfully!";
    $values["verification_code"] = $code;
    echo json_encode($values);
    exit;

} catch (MailerSendException $e) {
    error_log('MailerSend Error: ' . $e->getMessage());
    error_log($email);
    $values["status"] = "Success"; // Change to "Failed" if you want to indicate failure only using success to skip mailersend as trial is over.
    $values["message"] = "Error sending email: Verification Code: ${code}" . $e->getMessage();
    $values["verification_code"] = $code;
    echo json_encode($values);
    exit;
}

?>
