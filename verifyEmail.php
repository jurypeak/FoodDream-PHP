<?php

require 'vendor/autoload.php';
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Exceptions\MailerSendException;
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
    $mailersend = new MailerSend(['api_key' => 'key']);

    $recipients = [
        new Recipient($email, 'Customer'),
    ];

    $personalization = [
        new Personalization($email, [
            'support_email' => 'fooddreamsupport@jurypeak.com',
            'verification_code' => $code
        ])
    ];

    $emailParams = (new EmailParams())
        ->setFrom('fooddreamsupport@jurypeak.com')
        ->setFromName('Food Dream Support')
        ->setRecipients($recipients)
        ->setSubject('Email Verification')
        ->setTemplateId('7dnvo4d8qwrl5r86')
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
    $values["status"] = "Failed";
    $values["message"] = "Error sending email: " . $e->getMessage();
    echo json_encode($values);
    exit;
}

?>
