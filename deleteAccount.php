<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'conn.php'; // your DB connection

    // Get raw JSON input
    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    $accountID = $data['accountId'];

    // Validate AccountID
    if (empty($accountID)) {
        echo json_encode([
            "status" => "Failed",
            "message" => "Account ID is required."
        ]);
        exit;
    }

    // Prepare delete statement
    $sql = "DELETE FROM Account WHERE AccountID = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $accountID);
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo json_encode([
                    "status" => "Success",
                    "message" => "Account deleted successfully."
                ]);
            } else {
                echo json_encode([
                    "status" => "Failed",
                    "message" => "Account not found or already deleted."
                ]);
            }
        } else {
            echo json_encode([
                "status" => "Failed",
                "message" => "Failed to delete account."
            ]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode([
            "status" => "Failed",
            "message" => "Database error: could not prepare statement."
        ]);
    }

    mysqli_close($conn);
}
?>

