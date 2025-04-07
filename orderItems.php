<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'conn.php';

    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    if (!isset($data['orderId'], $data['productId'], $data['quantity'], $data['price'], $data['productName'])) {
        echo json_encode(["status" => "Failed", "message" => "All fields are required."]);
        exit;
    }

    $orderId = $data['orderId'];
    $productId = $data['productId'];
    $quantity = $data['quantity'];
    $price = $data['price'];
    $productName = $data['productName'];

    $sql_insert = "INSERT INTO `OrderItems` (OrderID, ProductID, Quantity, Price, ItemName) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "sssss", $orderId, $productId, $quantity, $price, $productName);

    if (mysqli_stmt_execute($stmt_insert)) {
        echo json_encode(["status" => "Success", "message" => "Order item $productName saved successfully!"]);
    } else {
        error_log("Error inserting data into the database: " . mysqli_error($conn));
        echo json_encode(["status" => "Failed", "message" => "Error inserting data into the database."]);
    }

    mysqli_stmt_close($stmt_insert);
    mysqli_close($conn);
}
?>
