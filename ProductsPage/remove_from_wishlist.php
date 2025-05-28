<?php
session_start();
require '../connection.php'; // Adjust path if needed
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

    $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    $session_id = session_id();

    // Determine the user identifier for the query
    $bound_user_id = $user_id; // Will be null if guest, actual ID if logged in

    // Determine if logged in or guest for deletion
    $sql_delete = "DELETE FROM wishlist WHERE product_id = ? AND ";
    $delete_params = [$product_id];
    $delete_types = "i";

    if ($user_id) {
        $sql_delete .= "user_id = ?";
        $delete_params[] = $bound_user_id;
        $delete_types .= "i";
    } else {
        $sql_delete .= "session_id = ?";
        $delete_params[] = $session_id;
        $delete_types .= "s";
    }

    $stmt_delete = $con->prepare($sql_delete);
    // Dynamically bind parameters for delete statement
    call_user_func_array([$stmt_delete, 'bind_param'], array_merge([$delete_types], $delete_params));

    if ($stmt_delete->execute()) {
        if ($stmt_delete->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Item removed from wishlist.';
        } else {
            $response['message'] = 'Item not found in wishlist or not authorized.';
        }
    } else {
        $response['message'] = 'Failed to remove item: ' . $con->error;
    }
    $stmt_delete->close();

    // Get updated wishlist count
    $wishlist_count = 0;
    $sql_count = "SELECT COUNT(*) as total FROM wishlist WHERE ";
    $count_params = [];
    $count_types = "";

    if ($user_id) {
        $sql_count .= "user_id = ?";
        $count_params[] = $bound_user_id;
        $count_types .= "i";
    } else {
        $sql_count .= "session_id = ?";
        $count_params[] = $session_id;
        $count_types .= "s";
    }
    $stmt_count = $con->prepare($sql_count);
    call_user_func_array([$stmt_count, 'bind_param'], array_merge([$count_types], $count_params));
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    if ($row_count = $result_count->fetch_assoc()) {
        $wishlist_count = $row_count['total'] ?? 0;
    }
    $stmt_count->close();
    $response['wishlist_count'] = $wishlist_count;

} else {
    $response['message'] = 'Product ID not provided for removal.';
}

echo json_encode($response);
$con->close();
exit();
?>