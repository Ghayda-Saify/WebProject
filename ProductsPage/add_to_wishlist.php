<?php
session_start();
require_once('../connection.php');
global $con;
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to wishlist']);
    exit;
}

if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing product ID']);
    exit;
}

$user_id = $_SESSION['user']['id'];
$product_id = intval($_POST['product_id']);

// Check if connection is valid
if ($con->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Check if product exists
    $stmt = $con->prepare("SELECT id FROM product WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    // Check if product is already in wishlist
    $stmt = $con->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Remove from wishlist
        $stmt = $con->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $message = 'Product removed from wishlist';
        $in_wishlist = false;
    } else {
        // Add to wishlist
        $stmt = $con->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $message = 'Product added to wishlist';
        $in_wishlist = true;
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'in_wishlist' => $in_wishlist
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating wishlist']);
}
?> 