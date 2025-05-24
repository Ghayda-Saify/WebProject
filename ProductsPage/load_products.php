<?php
// Use your existing connection file
include '../connection.php';
global $con;
// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 36;

$sql = "SELECT * FROM product WHERE status = 1 LIMIT $offset, $limit";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-all cursor-pointer product-card group"
                 data-name="' . htmlspecialchars($row["name"]) . '" 
                 data-price="' . htmlspecialchars($row["price"]) . '"
                 data-description="' . htmlspecialchars($row["short_desc"]) . '"
                 data-images=\'["imgs/' . htmlspecialchars($row["image"]) . '"]\'>';

        echo '<div class="h-64 overflow-hidden relative">';
        echo '<img src="imgs/' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["name"]) . '" 
                 class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">';
        echo '<div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300 flex items-center justify-center">';
        echo '<button class="view-details bg-primary text-white px-6 py-2 rounded-full opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">';
        echo 'View Details';
        echo '</button>';
        echo '</div>';
        echo '</div>';

        echo '<div class="p-6">';
        echo '<h3 class="font-aboreto text-xl mb-2">' . htmlspecialchars($row["name"]) . '</h3>';
        echo '<p class="text-gray-600 mb-2">' . htmlspecialchars($row["short_desc"]) . '</p>';
        echo '<p class="text-primary font-aboreto text-lg">' . htmlspecialchars($row["price"]) . ' SR</p>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<div class="col-span-full text-center py-8">';
    echo '<p class="text-gray-500">No more products to load.</p>';
    echo '</div>';
}

$con->close();
?>