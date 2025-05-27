<?php
session_start();
require_once('../connection.php');

header('Content-Type: application/json');

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$price_range = isset($_GET['price']) ? $_GET['price'] : null;
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

try {
    $query = "SELECT p.*, 
                     GROUP_CONCAT(DISTINCT pi.image) as images,
                     GROUP_CONCAT(DISTINCT c.id) as category_ids,
                     GROUP_CONCAT(DISTINCT c.name) as category_names,
                     CASE WHEN w.id IS NOT NULL THEN 1 ELSE 0 END as in_wishlist
              FROM product p
              LEFT JOIN product_images pi ON p.id = pi.product_id
              LEFT JOIN product_categories pc ON p.id = pc.product_id
              LEFT JOIN categories c ON pc.category_id = c.id
              LEFT JOIN wishlist w ON p.id = w.product_id AND w.user_id = ?";
    
    $params = [$user_id];
    $types = "i";
    
    $where_conditions = [];
    
    if ($category_id) {
        $where_conditions[] = "EXISTS (SELECT 1 FROM product_categories pc2 WHERE pc2.product_id = p.id AND pc2.category_id = ?)";
        $params[] = $category_id;
        $types .= "i";
    }
    
    if ($price_range) {
        switch ($price_range) {
            case 'under_50':
                $where_conditions[] = "p.price < 50";
                break;
            case '50_to_100':
                $where_conditions[] = "p.price >= 50 AND p.price <= 100";
                break;
            case '100_to_200':
                $where_conditions[] = "p.price > 100 AND p.price <= 200";
                break;
            case 'over_200':
                $where_conditions[] = "p.price > 200";
                break;
        }
    }
    
    if ($search_query) {
        $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
        $search_param = "%$search_query%";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "ss";
    }
    
    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    $query .= " GROUP BY p.id ORDER BY p.id DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $row['images'] = $row['images'] ? explode(',', $row['images']) : [];
        $row['category_ids'] = $row['category_ids'] ? explode(',', $row['category_ids']) : [];
        $row['category_names'] = $row['category_names'] ? explode(',', $row['category_names']) : [];
        $row['in_wishlist'] = (bool)$row['in_wishlist'];
        $products[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching products'
    ]);
}
?> 