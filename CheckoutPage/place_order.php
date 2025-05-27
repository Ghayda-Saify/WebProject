<?php
session_start();
require('../connection.php');
global $con;
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// 1. التأكد من أن المستخدم مسجل دخول
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}
$user_id = $_SESSION['user']['id'];

// 2. التحقق من بيانات الشحن
$required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit();
    }
}

// 3. فلترة بيانات الشحن
$first_name = mysqli_real_escape_string($con, $_POST['first_name']);
$last_name  = mysqli_real_escape_string($con, $_POST['last_name']);
$email      = mysqli_real_escape_string($con, $_POST['email']);
$phone      = mysqli_real_escape_string($con, $_POST['phone']);
$address    = mysqli_real_escape_string($con, $_POST['address']);
$city       = mysqli_real_escape_string($con, $_POST['city']);
$state      = mysqli_real_escape_string($con, $_POST['state']);
$zip        = mysqli_real_escape_string($con, $_POST['zip']);
$added_on   = date('Y-m-d H:i:s');

// 4. إحضار المنتجات من السلة (cart)
$cart_query = mysqli_query($con, "SELECT * FROM cart WHERE user_id = '$user_id'");
if (mysqli_num_rows($cart_query) == 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit();
}

// 5. إدخال الطلب إلى جدول orders
mysqli_query($con, "INSERT INTO orders (user_id, first_name, last_name, email, phone, address, city, state, zip, created_at) 
VALUES ('$user_id', '$first_name', '$last_name', '$email', '$phone', '$address', '$city', '$state', '$zip', '$added_on')");

$order_id = mysqli_insert_id($con); // جلب رقم الطلب الجديد

// 6. نسخ محتوى السلة إلى جدول order_items
while ($row = mysqli_fetch_assoc($cart_query)) {
    $product_id = $row['product_id'];
    $qty = isset($row['quantity']) ? $row['quantity'] : (isset($row['qty']) ? $row['qty'] : 1);

    // التأكد من وجود المنتج ومعرفة السعر
    $product_res = mysqli_query($con, "SELECT price FROM product WHERE id = '$product_id' LIMIT 1");
    if (mysqli_num_rows($product_res) == 0) continue; // تخطي المنتج إذا لم يوجد

    $product_row = mysqli_fetch_assoc($product_res);
    $price = $product_row['price'];

    // إدخال بيانات المنتج ضمن order_items
    mysqli_query($con, "INSERT INTO order_items (order_id, product_id, quantity, price)
                        VALUES ('$order_id', '$product_id', '$qty', '$price')");
}

// 7. حذف كل المنتجات من جدول cart
mysqli_query($con, "DELETE FROM cart WHERE user_id = '$user_id'");

// 8. إرجاع رد للمستخدم
echo json_encode([
    'success' => true,
    'order_id' => $order_id,
    'message' => 'تمت عملية الشراء بنجاح! سيصلك الطرد خلال 2-3 أيام.'
]);
?>