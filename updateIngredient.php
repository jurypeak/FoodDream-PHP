<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'conn.php';

    // Extract the incoming request data (JSON)
    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    // Validate required fields
    if (!isset($data['ingredientId'], $data['productId'], $data['name'], $data['weight'], $data['allergens'])) {
        echo json_encode(["status" => "Failed", "message" => "All fields are required."]);
        exit;
    }

    // Assign variables
    $ingredientId = $data['ingredientId'];
    $productId = $data['productId'];
    $name = $data['name'];
    $weight = $data['weight'];
    $allergens = $data['allergens'];

    // Check if the ingredient exists for the given productId
    $sql_check = "SELECT IngredientID FROM Ingredient WHERE ProductID = ? AND IngredientID = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $productId, $ingredientId);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    // Check if the ingredient exists
    if (mysqli_num_rows($result_check) == 0) {
        echo json_encode(["status" => "Failed", "message" => "Ingredient not found for the given product."]);
        exit;
    }

    // Update the ingredient in the database
    $sql_update = "UPDATE Ingredient SET IngredientName = ?, Weight = ?, Allergens = ? WHERE IngredientID = ? AND ProductID = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "sdssi", $name, $weight, $allergens, $ingredientId, $productId);

    if (mysqli_stmt_execute($stmt_update)) {
        echo json_encode(["status" => "Success", "message" => "Ingredient updated successfully."]);
    } else {
        error_log("Error updating data: " . mysqli_error($conn));
        echo json_encode(["status" => "Failed", "message" => "Error updating ingredient in the database."]);
    }

    // Close statements and database connection
    mysqli_stmt_close($stmt_check);
    mysqli_stmt_close($stmt_update);
    mysqli_close($conn);
}
?>

