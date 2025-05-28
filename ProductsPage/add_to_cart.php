<?php
session_start();
include '../connection.php'; // Adjust path if needed
global $con;

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request', 'cart_count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'], $_POST['size'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $size = htmlspecialchars($_POST['size']); // Sanitize size input

    if ($product_id <= 0 || $quantity <= 0) {
        $response['message'] = 'Invalid product ID or quantity.';
        echo json_encode($response);
        exit;
    }

    // Determine user_id or session_id
    $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    $session_id = session_id();

    // Check if product already exists in cart for the current user/session
    $sql_check = "SELECT id, quantity FROM cart WHERE product_id = ? AND ";
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
        // Product exists, update quantity
        $row = $result_check->fetch_assoc();
        $cart_item_id = $row['id'];
        $new_quantity = $row['quantity'] + $quantity;

        $sql_update = "UPDATE cart SET quantity = ? WHERE id = ?";
        $stmt_update = $con->prepare($sql_update);
        $stmt_update->bind_param("ii", $new_quantity, $cart_item_id);

        if ($stmt_update->execute()) {
            $response['success'] = true;
            $response['message'] = 'Product quantity updated in cart!';
        } else {
            $response['message'] = 'Failed to update cart quantity: ' . $con->error;
        }
        $stmt_update->close();
    } else {
        // Product does not exist, insert new record
        $sql_insert = "INSERT INTO cart (product_id, quantity, user_id, session_id, size) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $con->prepare($sql_insert);

        // Handle potentially null user_id by binding it as null or a default (e.g., 0) if your DB allows
        // For typical INT columns, binding null might require specific PDO or NULL-safe types.
        // For simplicity and common scenarios, let's assume user_id is INT and handles NULL well or we pass 0.
        // It's better to ensure your 'user_id' column in 'cart' table is nullable or has a default for guest checkouts.
        $bound_user_id = $user_id !== null ? $user_id : 0; // Use 0 for guest if user_id cannot be null

        $stmt_insert->bind_param("iiiss", $product_id, $quantity, $bound_user_id, $session_id, $size);

        if ($stmt_insert->execute()) {
            $response['success'] = true;
            $response['message'] = 'Product added to cart successfully!';
        } else {
            $response['message'] = 'Failed to add product to cart: ' . $con->error;
        }
        $stmt_insert->close();
    }
    $stmt_check->close();

    // Get updated cart count
    $cart_count = 0;
    $sql_count = "SELECT SUM(quantity) as total FROM cart WHERE ";
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
        $cart_count = $row_count['total'] ?? 0;
    }
    $stmt_count->close();
    $response['cart_count'] = $cart_count;

} else {
    $response['message'] = 'Required parameters are missing.';
}

echo json_encode($response);
$con->close();
?>