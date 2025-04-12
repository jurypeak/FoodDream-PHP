<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'conn.php';

    // Extract the incoming request data (JSON)
    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    // Validate required fields
    if (!isset($data['productId'], $data['name'], $data['weight'])) {
        echo json_encode(["status" => "Failed", "message" => "All fields are required."]);
        exit;
    }

    // Assign variables
    $productId = $data['productId'];
    $name = $data['name'];
    $weight = $data['weight'];
    $allergens = $data['allergens'];

    // Insert new user into the database
    $sql_insert = "INSERT INTO Ingredient (ProductID, IngredientName, Weight, Allergens) VALUES (?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "isds", $productId, $name, $weight, $allergens);

    if (mysqli_stmt_execute($stmt_insert)) {
        echo json_encode(["status" => "Success", "message" => "Addition successful!"]);
    } else {
        error_log("Error inserting data: " . mysqli_error($conn));
        echo json_encode(["status" => "Failed", "message" => "Error inserting data into the database."]);
    }

    mysqli_stmt_close($stmt_insert);
    mysqli_close($conn);
}
?>
