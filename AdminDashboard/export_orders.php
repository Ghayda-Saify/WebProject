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

// Fetch orders with customer names and product names
$sql = "
    SELECT 
        o.order_id,
        CONCAT(u.first_name, ' ', u.last_name) AS customer,
        GROUP_CONCAT(p.name SEPARATOR ', ') AS products,
        o.total_price,
        o.status,
        o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN product p ON oi.product_id = p.id
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
";

$result = $conn->query($sql);

// Set headers to force download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="orders_export.csv"');

$output = fopen('php://output', 'w');

// Add CSV header row
fputcsv($output, ['Order ID', 'Customer', 'Products', 'Total Price', 'Status', 'Date']);

// Output each row
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        '#' . $row['order_id'],
        $row['customer'],
        $row['products'],
        '₪' . number_format($row['total_price'], 2),
        $row['status'],
        date('Y-m-d H:i', strtotime($row['created_at']))
    ]);
}

fclose($output);
exit;