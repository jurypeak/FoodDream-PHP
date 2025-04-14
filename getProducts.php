<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Include database connection
    include 'conn.php';

    // Check if the connection was successful
    $sql = "
SELECT 
    p.ProductID, p.ProductName, p.ProductPrice, 
    p.ProductCO, p.ProductStock, p.Description, 
    p.Category, p.ImageURL,
    i.IngredientID, i.IngredientName, i.Weight, i.Allergens
FROM Product p
LEFT JOIN Ingredient i ON p.ProductID = i.ProductID
ORDER BY p.ProductID;
 ";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        $products = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $productId = $row['ProductID'];

            if (!isset($products[$productId])) {
                $products[$productId] = [
                    "id" => $productId,
                    "name" => $row['ProductName'],
                    "price" => $row['ProductPrice'],
                    "co" => $row['ProductCO'],
                    "stock" => $row['ProductStock'],
                    "description" => $row['Description'],
                    "category" => $row['Category'],
                    "image" => $row['ImageURL'],
                    "ingredients" => []
                ];
            }

            if (!empty($row['IngredientID'])) {
                $products[$productId]["ingredients"][] = [
                    "id" => $row['IngredientID'],
                    "name" => $row['IngredientName'],
                    "weight" => $row['Weight'],
                    "allergens" => $row['Allergens']
                ];
            }
        }

        // Return the products as a JSON response
        echo json_encode(array_values($products), JSON_PRETTY_PRINT);

    } else {
        echo json_encode(["status" => "Failed", "message" => "Database error: " . mysqli_error($conn)]);
    }

    mysqli_close($conn);
}
?>
