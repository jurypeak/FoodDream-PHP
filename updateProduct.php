<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'conn.php';

    // Extract the incoming request data (JSON)
    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    // Validate required fields
    if (!isset($data['id'], $data['name'], $data['description'], $data['price'], $data['category'], $data['stock'], $data['co'], $data['image'])) {
        echo json_encode(["status" => "Failed", "message" => "All fields are required."]);
        exit;
    }

    // Assign variables
    $id = $data['id'];  // Product ID to update
    $name = $data['name'];
    $description = $data['description'];
    $price = $data['price'];
    $category = $data['category'];
    $stock = $data['stock'];
    $co = $data['co'];
    $imageURL = $data['image'];

    // Check if product exists
    $sql_check = "SELECT ProductID FROM Product WHERE ProductID = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $id);  // Assuming ProductID is an integer
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) == 0) {
        echo json_encode(["status" => "Failed", "message" => "Product not found."]);
        exit;
    }

    // Update product details in the database
    $sql_update = "UPDATE Product SET ProductName = ?, ProductPrice = ?, ProductCO = ?, ProductStock = ?, Description = ?, Category = ?, ImageURL = ? WHERE ProductID = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "sssdsssi", $name, $price, $co, $stock, $description, $category, $imageURL, $id);

    if (mysqli_stmt_execute($stmt_update)) {
        echo json_encode(["status" => "Success", "message" => "Product updated successfully!"]);
    } else {
        error_log("Error updating data: " . mysqli_error($conn));
        echo json_encode(["status" => "Failed", "message" => "Error updating product data in the database."]);
    }

    // Close statements and database connection
    mysqli_stmt_close($stmt_check);
    mysqli_stmt_close($stmt_update);
    mysqli_close($conn);
}
?>

