<?php
global $con;
session_start();
include '../connection.php';

if (isset($_POST['cart_id'], $_POST['quantity'])) {
    $cart_id = (int)$_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        $stmt = $con->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $cart_id);
        $stmt->execute();
    }
}
header("Location: cart.php");
exit;
