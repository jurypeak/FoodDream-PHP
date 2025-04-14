<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Include your database connection file
    include 'conn.php';

    // Get raw JSON input
    $sql = "
    SELECT 
        o.OrdersID,
        o.AccountID,
        a.CustomerFName,
        a.CustomerLName,
        a.Email,
        o.OrderDate,

        oi.OrderItemsID,
        oi.ProductID,
        oi.Quantity,
        oi.Price,
        p.ProductName,

        pay.PaymentID,
        pay.PaymentMethod,
        pay.PaymentDate,
        pay.Amount,

        addr.AddressID,
        addr.Street,
        addr.Town,
        addr.Postcode

    FROM Orders o
    INNER JOIN Account a ON o.AccountID = a.AccountID
    LEFT JOIN OrderItems oi ON o.OrdersID = oi.OrderID
    LEFT JOIN Product p ON oi.ProductID = p.ProductID
    LEFT JOIN Payment pay ON o.OrdersID = pay.OrderID
    LEFT JOIN Address addr ON o.OrdersID = addr.OrderID
    ORDER BY o.OrdersID;
    ";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        $orders = [];

        // Fetch the results and organize them into an associative array
        while ($row = mysqli_fetch_assoc($result)) {
            $orderId = $row['OrdersID'];

            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    "id" => $orderId,
                    "accountId" => $row['AccountID'],
                    "fName" => $row['CustomerFName'],
                    "lName" => $row['CustomerLName'],
                    "email" => $row['Email'],
                    "date" => date("Y-m-d H:i:s", strtotime($row['OrderDate'])),
                    "orderItems" => [],
                    "payment" => null,
                    "address" => null
                ];
            }

            if (!empty($row['OrderItemsID'])) {
                $orders[$orderId]["orderItems"][] = [
                    "id" => $row['OrderItemsID'],
                    "productId" => $row['ProductID'],
                    "quantity" => $row['Quantity'],
                    "price" => $row['Price'],
                    "itemName" => $row['ProductName']
                ];
            }

            if ($orders[$orderId]["payment"] === null && !empty($row['PaymentID'])) {
                $orders[$orderId]["payment"] = [
                    "id" => $row['PaymentID'],
                    "paymentMethod" => $row['PaymentMethod'],
                    "paymentDate" => date("Y-m-d H:i:s", strtotime($row['PaymentDate'])),
                    "amount" => $row['Amount']
                ];
            }

            if ($orders[$orderId]["address"] === null && !empty($row['AddressID'])) {
                $orders[$orderId]["address"] = [
                    "id" => $row['AddressID'],
                    "street" => $row['Street'],
                    "town" => $row['Town'],
                    "postcode" => $row['Postcode']
                ];
            }
        }

        // Return the orders as a JSON response
        echo json_encode(array_values($orders), JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            "status" => "Failed",
            "message" => "Database error: " . mysqli_error($conn)
        ]);
    }

    mysqli_close($conn);
}
?>

