<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'conn.php';

    $jsondata = file_get_contents("php://input");
    $data = json_decode($jsondata, true);

    if (!isset($data['productId'])) {
        echo json_encode(["status" => "Failed", "message" => "Product ID is required."]);
        exit;
    }

    $productId = $data['productId'];

    $sql_check = "SELECT ProductID FROM Product WHERE ProductID = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $productId);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) == 0) {
        echo json_encode(["status" => "Failed", "message" => "Product not found."]);
        exit;
    }

    $sql_delete = "DELETE FROM Product WHERE ProductID = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $productId);

    if (mysqli_stmt_execute($stmt_delete)) {
        echo json_encode(["status" => "Success", "message" => "Product deleted successfully!"]);
    } else {
        error_log("Error deleting data: " . mysqli_error($conn));
        echo json_encode(["status" => "Failed", "message" => "Error deleting product from the database."]);
    }

    mysqli_stmt_close($stmt_check);
    mysqli_stmt_close($stmt_delete);
    mysqli_close($conn);
}
?>

