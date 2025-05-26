<?php
global $con;
session_start();
include '../connection.php';

if (isset($_POST['cart_id'])) {
    $cart_id = (int)$_POST['cart_id'];

    $stmt = $con->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
}
header("Location: cart.php");
exit;
