<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'conn.php';

    // Get the JSON data from request
    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    // Validate required fields
    if (!isset($data['orderId'], $data['productId'], $data['quantity'], $data['price'], $data['productName'])) {
        echo json_encode(["status" => "Failed", "message" => "All fields are required."]);
        exit;
    }

    // Extract values
    $orderId = (int)$data['orderId'];
    $productId = (int)$data['productId'];
    $quantity = (int)$data['quantity'];
    $price = (float)$data['price'];
    $productName = $data['productName'];

    // Insert into OrderItems
    $sql_insert = "INSERT INTO `OrderItems` (OrderID, ProductID, Quantity, Price, ItemName) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);

    if ($stmt_insert === false) {
        error_log("Prepare failed: " . mysqli_error($conn));
        echo json_encode(["status" => "Failed", "message" => "Failed to prepare insert statement."]);
        exit;
    }

    // Bind parameters
    mysqli_stmt_bind_param($stmt_insert, "iiids", $orderId, $productId, $quantity, $price, $productName);

    // Execute the statement
    if (mysqli_stmt_execute($stmt_insert)) {
        mysqli_stmt_close($stmt_insert);

        // Update stock in Product table
        $sql_update_stock = "UPDATE Product SET ProductStock = ProductStock - ? WHERE ProductID = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update_stock);

        if ($stmt_update === false) {
            error_log("Prepare failed: " . mysqli_error($conn));
            echo json_encode(["status" => "Failed", "message" => "Failed to prepare stock update."]);
            exit;
        }

        mysqli_stmt_bind_param($stmt_update, "ii", $quantity, $productId);

        if (mysqli_stmt_execute($stmt_update)) {
            echo json_encode([
                "status" => "Success",
                "message" => "Order item '$productName' saved and stock updated!"
            ]);
        } else {
            error_log("Stock update error: " . mysqli_error($conn));
            echo json_encode([
                "status" => "Partial Success",
                "message" => "Order saved, but failed to update stock."
            ]);
        }

        mysqli_stmt_close($stmt_update);
    } else {
        error_log("Insert error: " . mysqli_error($conn));
        echo json_encode([
            "status" => "Failed",
            "message" => "Failed to insert order item."
        ]);
    }

    mysqli_close($conn);
}
?>

