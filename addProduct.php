<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'conn.php';

    // Extract the incoming request data (JSON)
    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    // Validate required fields
    if (!isset($data['name'], $data['description'], $data['price'], $data['category'], $data['stock'], $data['co'], $data['image'])) {
        echo json_encode(["status" => "Failed", "message" => "All fields are required."]);
        exit;
    }

    // Assign variables
    $name = $data['name'];
    $description = $data['description'];
    $price = $data['price'];
    $category = $data['category'];
    $stock = $data['stock'];
    $co = $data['co'];
    $imageURL = $data['image'];

    // Check if product name already exists
    $sql_check = "SELECT ProductID FROM Product WHERE ProductName = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $name);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo json_encode(["status" => "Failed", "message" => "Product name already exists."]);
        exit;
    }

    // Insert new user into the database
    $sql_insert = "INSERT INTO Product (ProductName, ProductPrice, ProductCO, ProductStock, Description, Category, ImageURL) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "sssdsss", $name, $price, $co, $stock, $description, $category, $imageURL);

    if (mysqli_stmt_execute($stmt_insert)) {
        // Get the ID of the newly inserted record
        $productId = mysqli_insert_id($conn);
        echo json_encode(["status" => "Success", "message" => "Addition successful!", "id" => $productId]);
    } else {
        error_log("Error inserting data: " . mysqli_error($conn));
        echo json_encode(["status" => "Failed", "message" => "Error inserting data into the database."]);
    }

    // Close statements and database connection
    mysqli_stmt_close($stmt_check);
    mysqli_stmt_close($stmt_insert);
    mysqli_close($conn);
}
?>
