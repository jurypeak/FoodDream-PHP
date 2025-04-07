<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'conn.php';

    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    if (!isset($data['email'], $data['accountId'], $data['fName'], $data['lName'])) {
        echo json_encode(["status" => "Failed", "message" => "All fields are required."]);
        exit;
    }

    $email = $data['email'];
    $fName = $data['fName'];
    $lName = $data['lName'];
    $accountId = (int)$data['accountId'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "Failed", "message" => "Invalid email format."]);
        exit;
    }

    $sql_insert = "INSERT INTO `Orders` (AccountID, FName, LName, Email) VALUES (?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "ssss", $accountId, $fName, $lName, $email);

    if (mysqli_stmt_execute($stmt_insert)) {
        echo json_encode(["status" => "Success", "message" => "Order successful!", "orderId" => mysqli_insert_id($conn)]);
    } else {
        error_log("Error inserting data into the database: " . mysqli_error($conn));
        echo json_encode(["status" => "Failed", "message" => "Error inserting data into the database."]);
    }

    mysqli_stmt_close($stmt_insert);
    mysqli_close($conn);
}
?>
