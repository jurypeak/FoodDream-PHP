<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection
    include 'conn.php';

    // Extract the incoming request data (JSON)
    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);
    $email = $data['email'];
    $password = $data['password'];

    // Check if email or password is empty
    if (empty($email) || empty($password)) {
        $values["status"] = "Failed";
        $values["message"] = "Email and password are required.";
        echo json_encode($values);
        exit;
    }

    // Prepared statement to retrieve user by email
    $sql = "SELECT * FROM Account WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // If user found
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $values["status"] = "Success";
        $values["email"] = $row['Email'];
        $values["id"] = $row['AccountID'];
        $values["CustomerFName"] = $row['CustomerFName'];
        $values["CustomerLName"] = $row['CustomerLName'];
        $values["accessLevel"] = $row['AccessLevel'];
        $values["password"] = $row['Password'];

    } else {
        $values["status"] = "Failed";
        $values["message"] = "Email not found.";
    }

    // Return response as JSON
    echo json_encode($values);
}
?>

