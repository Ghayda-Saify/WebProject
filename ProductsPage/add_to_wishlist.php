<?php
session_start();
include '../connection.php'; // Adjust path if needed
global $con;

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request', 'wishlist_count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];

    if ($product_id <= 0) {
        $response['message'] = 'Invalid product ID.';
        echo json_encode($response);
        exit;
    }

    // Determine user_id or session_id
    $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    $session_id = session_id();

    // Check if product already exists in wishlist for the current user/session
    $sql_check = "SELECT id FROM wishlist WHERE product_id = ? AND ";
    if ($user_id) {
        $sql_check .= "user_id = ?";
    } else {
        $sql_check .= "session_id = ?";
    }
    $stmt_check = $con->prepare($sql_check);

    if ($user_id) {
        $stmt_check->bind_param("ii", $product_id, $user_id);
    } else {
        $stmt_check->bind_param("is", $product_id, $session_id);
    }
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $response['success'] = true; // Already in wishlist can be considered a "success" for not needing to add again
        $response['message'] = 'Product is already in your wishlist!';
    } else {
        // Product does not exist, insert new record
        $sql_insert = "INSERT INTO wishlist (user_id, session_id, product_id) VALUES (?, ?, ?)";
        $stmt_insert = $con->prepare($sql_insert);

        $bound_user_id = $user_id !== null ? $user_id : 0;

        $stmt_insert->bind_param("isi", $bound_user_id, $session_id, $product_id);

        if ($stmt_insert->execute()) {
            $response['success'] = true;
            $response['message'] = 'Product added to wishlist successfully!';
        } else {
            $response['message'] = 'Failed to add product to wishlist: ' . $con->error;
        }
        $stmt_insert->close();
    }
    $stmt_check->close();

    // Get updated wishlist count
    $wishlist_count = 0;
    $sql_count = "SELECT COUNT(*) as total FROM wishlist WHERE ";
    if ($user_id) {
        $sql_count .= "user_id = ?";
        $stmt_count = $con->prepare($sql_count);
        $stmt_count->bind_param("i", $user_id);
    } else {
        $sql_count .= "session_id = ?";
        $stmt_count = $con->prepare($sql_count);
        $stmt_count->bind_param("s", $session_id);
    }
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    if ($row_count = $result_count->fetch_assoc()) {
        $wishlist_count = $row_count['total'] ?? 0;
    }
    $stmt_count->close();
    $response['wishlist_count'] = $wishlist_count;

} else {
    $response['message'] = 'Required product ID is missing.';
}

echo json_encode($response);
$con->close();
?>