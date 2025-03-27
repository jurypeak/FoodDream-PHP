<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    include 'conn.php';

    $sql = "
SELECT 
    p.ProductID, p.ProductName, p.ProductPrice, 
    p.ProductCO, p.ProductStock, p.Description, 
    p.Category, p.ImageURL, p.Views, p.Sales,
    i.IngredientID, i.IngredientName, i.Weight, i.Allergens
FROM Product p
LEFT JOIN Ingredient i ON p.ProductID = i.ProductID
ORDER BY p.ProductID;
 ";

    $result = mysqli_query($conn, $sql);

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
                    "views" => $row['Views'],
                    "sales" => $row['Sales'],
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

        echo json_encode(array_values($products), JSON_PRETTY_PRINT);

    } else {
        echo json_encode(["status" => "Failed", "message" => "Database error: " . mysqli_error($conn)]);
    }

    mysqli_close($conn);
}
?>
