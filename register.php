<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection
    include 'conn.php';

    // Extract the incoming request data (JSON)
    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);
    $email = $data['email'];
    $fName = $data['fName'];
    $lName = $data['lName'];
    $password = $data['password'];
    $accessLevel = $data['accessLevel'];

    // Validate the incoming data
    if (!isset($data['email'], $data['password'], $data['fName'], $data['lName'])) {
        echo json_encode(["status" => "Failed", "message" => "All fields are required."]);
        exit;
    }

    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "Failed", "message" => "Invalid email format."]);
        exit;
    }

    // Check if email already exists in the database
    $sql_check = "SELECT * FROM Account WHERE Email = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    // If user already exists, send failure response
    if (mysqli_num_rows($result_check) > 0) {
        echo json_encode(["status" => "Failed", "message" => "Email already registered."]);
        exit;
    }

    // Prepare statement to insert new user into the database
    $sql_insert = "INSERT INTO Account (AccessLevel, Email, CustomerFName, CustomerLName, Password) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "sssss", $accessLevel, $email, $fName, $lName, $password);

    // Execute the statement and check for success
    if (mysqli_stmt_execute($stmt_insert)) {
        echo json_encode(["status" => "Success", "message" => "Registration successful!"]);
    } else {
        // Log the error (for debugging purposes) and return a generic error message
        error_log("Error inserting data into the database: " . mysqli_error($conn));
        echo json_encode(["status" => "Failed", "message" => "Error inserting data into the database."]);
    }

    // Close the statements and database connection
    mysqli_stmt_close($stmt_check);
    mysqli_stmt_close($stmt_insert);
    mysqli_close($conn);
}

