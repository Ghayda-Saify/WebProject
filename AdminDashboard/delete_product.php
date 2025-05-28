<?php
session_start();

// Database connection
$host = "127.0.0.1";
$user = "root"; // Update if needed
$password = ""; // Update accordingly
$dbname = "alandalus";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = intval($_GET['id'] ?? 0);

// Delete product
$delete_sql = "DELETE FROM product WHERE id = ?";
$stmt = $conn->prepare($delete_sql);
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    header("Location: products.php");
    exit;
} else {
    die("Error deleting product: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>