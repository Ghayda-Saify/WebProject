<?php
session_start();
include '../connection.php';
global $con;

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = mysqli_real_escape_string($con, $_GET['id']);

    // Fetch product details
    $sql = "SELECT * FROM product WHERE id = '$product_id' LIMIT 1";
    $result = $con->query($sql);

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();

        $response = ['success' => true, 'product' => $product];
    } else {
        $response = ['success' => false, 'message' => 'Product not found'];
    }
} else {
    $response = ['success' => false, 'message' => 'Product ID not provided'];
}

echo json_encode($response);

$con->close();
?> 