<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'conn.php';

    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    if (!isset($data['orderId'], $data['paymentMethod'], $data['amount'])) {
        echo json_encode(["status" => "Failed", "message" => "All fields are required."]);
        exit;
    }

    $orderId = $data['orderId'];
    $paymentMethod = $data['paymentMethod'];
    $amount = $data['amount'];

    $sql_insert = "INSERT INTO `Payment` (OrderID, PaymentMethod, Amount) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "sss", $orderId, $paymentMethod, $amount);

    if (mysqli_stmt_execute($stmt_insert)) {
        echo json_encode(["status" => "Success", "message" => "Payment was successful!"]);
    } else {
        error_log("Error inserting data into the database: " . mysqli_error($conn));
        echo json_encode(["status" => "Failed", "message" => "Error inserting data into the database."]);
    }

    mysqli_stmt_close($stmt_insert);
    mysqli_close($conn);
}
?>

