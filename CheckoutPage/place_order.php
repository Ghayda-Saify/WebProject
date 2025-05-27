<?php

session_start();
require('../connection.php');
global $con;
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}
$user_id = $_SESSION['user']['id'];

// Collect shipping info from POST
$required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit();
    }
}

$first_name = mysqli_real_escape_string($con, $_POST['first_name']);
$last_name  = mysqli_real_escape_string($con, $_POST['last_name']);
$email      = mysqli_real_escape_string($con, $_POST['email']);
$phone      = mysqli_real_escape_string($con, $_POST['phone']);
$address    = mysqli_real_escape_string($con, $_POST['address']);
$city       = mysqli_real_escape_string($con, $_POST['city']);
$state      = mysqli_real_escape_string($con, $_POST['state']);
$zip        = mysqli_real_escape_string($con, $_POST['zip']);
$added_on   = date('Y-m-d H:i:s');

// Retrieve cart from DB
$cart_query = mysqli_query($con, "SELECT * FROM cart WHERE user_id = '$user_id'");
if (mysqli_num_rows($cart_query) == 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit();
}

// Insert into orders table
mysqli_query($con, "INSERT INTO orders (user_id, fname, lname, email, mobile, address, city, state, zip, added_on) 
VALUES ('$user_id', '$first_name', '$last_name', '$email', '$phone', '$address', '$city', '$state', '$zip', '$added_on')");

// Get the last inserted order ID
$order_id = mysqli_insert_id($con);

// Insert order details
while ($row = mysqli_fetch_assoc($cart_query)) {
    $product_id = $row['product_id'];
    $qty = isset($row['quantity']) ? $row['quantity'] : (isset($row['qty']) ? $row['qty'] : 1);

    $product_res = mysqli_query($con, "SELECT price FROM product WHERE id = '$product_id' LIMIT 1");
    $product_row = mysqli_fetch_assoc($product_res);
    $price = $product_row['price'];

    mysqli_query($con, "INSERT INTO order_detail (order_id, product_id, qty, price) 
        VALUES ('$order_id', '$product_id', '$qty', '$price')");
}

// Clear user's cart
mysqli_query($con, "DELETE FROM cart WHERE user_id = '$user_id'");

echo json_encode(['success' => true, 'order_id' => $order_id]);
