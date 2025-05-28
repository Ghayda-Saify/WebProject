<?php
session_start();
include 'connection.php'; // Adjust path if connection.php is in a different directory
global $con;
header('Content-Type: application/json');

// Check if the connection is successful
if ($con->connect_error) {
    echo json_encode([]); // Return empty array on connection failure
    exit();
}

// Get product_id from GET request
$product_id = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);

if (!$product_id) {
    echo json_encode([]); // Return empty array if product_id is invalid
    exit();
}

$reviews = [];

// Prepare SQL statement to fetch reviews for the given product_id
// Order by created_at in descending order to show newest reviews first
$sql = "SELECT user_name, rating, comment, created_at FROM reviews WHERE product_id = ? ORDER BY created_at DESC";
$stmt = $con->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $product_id); // 'i' for integer product_id
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();
}

echo json_encode($reviews);

$con->close();
