<?php
session_start();
include '../connection.php';
global $con;
if (!isset($_SESSION['user'])) {
    echo "You must be logged in to view this page.";
    exit;
}

if (!isset($_GET['order_id'])) {
    echo "No order specified.";
    exit;
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user']['id'];

// Fetch order details
$order_sql = "SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id";
$order_result = $con->query($order_sql);

if ($order_result->num_rows === 0) {
    echo "Order not found.";
    exit;
}

$order = $order_result->fetch_assoc();

// Fetch order items
$items_sql = "SELECT oi.*, p.name 
              FROM order_items oi 
              JOIN product p ON oi.product_id = p.id 
              WHERE oi.order_id = $order_id";
$items_result = $con->query($items_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thank You - Order #<?php echo $order_id; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f9f9f9;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2E8B57;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Thank you for your order, <?php echo htmlspecialchars($order['full_name']); ?>!</h2>
    <p>Your order ID is <strong>#<?php echo $order_id; ?></strong>.</p>
    <p>We have sent the confirmation to <strong><?php echo htmlspecialchars($order['email']); ?></strong>.</p>
    <p>Shipping to: <br>
        <?php echo htmlspecialchars($order['address']) . ', ' . htmlspecialchars($order['city']) . ', ' . htmlspecialchars($order['state']) . ' - ' . htmlspecialchars($order['zip']); ?>
    </p>

    <h3>Order Summary</h3>
    <table>
        <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price (each)</th>
            <th>Subtotal</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        while ($item = $items_result->fetch_assoc()):
            $subtotal = $item['quantity'] * $item['price'];
            $total += $subtotal;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
                <td>$<?php echo number_format($subtotal, 2); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <p class="total">Total Paid: $<?php echo number_format($total, 2); ?></p>
</div>

</body>
</html>
