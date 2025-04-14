<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection
    include 'conn.php';

    // Extract the incoming request data (JSON)
    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    // Validate required fields
    $accountID = $data['accountId'];
    $email = $data['email'];
    $fName = $data['fName'];
    $lName = $data['lName'];
    $password = $data['password'];

    // Check if any required fields are empty
    if (empty($accountID) || empty($email) || empty($fName) || empty($password)) {
        echo json_encode([
            "status" => "Failed",
            "message" => "Missing required fields."
        ]);
        exit;
    }

    // Check if email exists on another account
    $checkEmailSql = "SELECT AccountID FROM Account WHERE Email = ? AND AccountID != ?";
    $checkStmt = mysqli_prepare($conn, $checkEmailSql);
    mysqli_stmt_bind_param($checkStmt, "ss", $email, $accountID);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkResult) > 0) {
        echo json_encode([
            "status" => "Failed",
            "message" => "Email is already in use by another account."
        ]);
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        exit;
    }
    mysqli_stmt_close($checkStmt);

    // Safe to update
    $sql = "UPDATE Account SET Email = ?, CustomerFName = ?, CustomerLName = ?, Password = ? WHERE AccountID = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $email, $fName, $lName, $password, $accountID);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode([
                "status" => "Success",
                "message" => "User updated successfully."
            ]);
        } else {
            echo json_encode([
                "status" => "Failed",
                "message" => "Could not execute update."
            ]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode([
            "status" => "Failed",
            "message" => "Could not prepare statement."
        ]);
    }

    mysqli_close($conn);
}
?>
