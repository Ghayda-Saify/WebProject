<?php
session_start();
require '../connection.php'; // Adjust path if needed
global $con;

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request', 'wishlist_count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];

    if ($product_id <= 0) {
        $response['message'] = 'Invalid product ID provided.'; // More specific message
        echo json_encode($response);
        exit;
    }

    // Determine user_id or session_id
    $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    $session_id = session_id();

    // Determine the user identifier for the query
    $bound_user_id = $user_id; // Will be null if guest, actual ID if logged in

    // Check if product already exists in wishlist for the current user/session
    $sql_check = "SELECT id FROM wishlist WHERE product_id = ? AND ";
    $check_params = [$product_id];
    $check_types = "i";

    if ($user_id) {
        $sql_check .= "user_id = ?";
        $check_params[] = $bound_user_id;
        $check_types .= "i";
    } else {
        $sql_check .= "session_id = ?";
        $check_params[] = $session_id;
        $check_types .= "s";
    }

    $stmt_check = $con->prepare($sql_check);
    // Dynamically bind parameters for check statement
    call_user_func_array([$stmt_check, 'bind_param'], array_merge([$check_types], $check_params));
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Product is already in your wishlist!';
    } else {
        // Product does not exist, insert new record
        $sql_insert = "INSERT INTO wishlist (user_id, session_id, product_id) VALUES (?, ?, ?)";
        $stmt_insert = $con->prepare($sql_insert);

        // Bind parameters for insert statement
        // Note: 's' for session_id, 'i' for product_id.
        // For user_id, if it's NULL, it will be bound as NULL. If your DB column is NOT NULL,
        // you MUST change $bound_user_id to be 0 instead of null.
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
    $response['message'] = 'Required product ID is missing in POST data.'; // More specific message
}

echo json_encode($response);
$con->close();
exit();
?>