<?php
session_start();
require_once('../connection.php');
global $con;
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

if (!isset($_POST['product_id']) || !isset($_POST['quantity']) || !isset($_POST['size'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$user_id = $_SESSION['user']['id'];
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);
$size = $_POST['size'];

// Validate quantity
if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

// Validate size
$valid_sizes = ['small', 'medium', 'large'];
if (!in_array($size, $valid_sizes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid size']);
    exit;
}

// Check if connection is valid
if ($con->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Check if product exists and is in stock
    $stmt = $con->prepare("SELECT qty FROM product WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    $product = $result->fetch_assoc();
    if ($product['qty'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit;
    }

    // Check if product is already in cart
    $stmt = $con->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND size = ?");
    $stmt->bind_param("iis", $user_id, $product_id, $size);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing cart item
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        if ($new_quantity > $product['qty']) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
            exit;
        }
        
        $stmt = $con->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
        $stmt->execute();
    } else {
        // Insert new cart item
        $stmt = $con->prepare("INSERT INTO cart (user_id, product_id, quantity, size) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $product_id, $quantity, $size);
        $stmt->execute();
    }

    // Get updated cart count
    $stmt = $con->prepare("SELECT SUM(quantity) as cart_count FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_count = $result->fetch_assoc()['cart_count'] ?? 0;

    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart successfully',
        'cart_count' => $cart_count
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while adding to cart: ' . $e->getMessage()
    ]);
}
?> 