<?php
session_start();
require '../connection.php'; // Adjust path if needed

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user']['id'];
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

// Check if product already in cart
$stmt = $con->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update quantity
    $update = $con->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
    $update->bind_param("iii", $quantity, $user_id, $product_id);
    $update->execute();
    echo json_encode(["status" => "success", "message" => "Cart updated"]);
} else {
    // Insert new
    $insert = $con->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $user_id, $product_id, $quantity);
    $insert->execute();
    echo json_encode(["status" => "success", "message" => "Product added to cart"]);
}
