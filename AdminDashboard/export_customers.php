<?php
session_start();

// Database connection
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "alandalus";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch customers
$sql = "SELECT id, name, email, phone_number, created_at FROM users WHERE type = 'user'";
$result = $conn->query($sql);

// Set headers to force download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="customers_export.csv"');

$output = fopen('php://output', 'w');

// Add CSV header row
fputcsv($output, ['Customer ID', 'Name', 'Email', 'Phone', 'Joined On']);

// Output each row
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['email'],
        $row['phone_number'] ?: 'N/A',
        date('Y-m-d H:i', strtotime($row['created_at']))
    ]);
}

fclose($output);
exit;