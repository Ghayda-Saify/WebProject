<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "alandalus";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchQuery = $conn->real_escape_string($_GET['query'] ?? '');

$sql = "SELECT * FROM product WHERE status = 1";

if (!empty($searchQuery)) {
    $sql .= " AND name LIKE '%" . $searchQuery . "%'";
}

$result = $conn->query($sql);

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => floatval($row['price']),
        'description' => $row['short_desc'] ?? '',
        'image' => $row['image']
    ];
}

echo json_encode(['success' => true, 'products' => $products]);

$conn->close();
?>