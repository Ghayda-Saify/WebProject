<?php
session_start();
include '../connection.php'; // Adjust path if connection.php is in a different directory
global $con; // Ensure $con is globally accessible if needed for the connection

header('Content-Type: application/json');

// Check if the connection is successful
if ($con->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $con->connect_error]);
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

    // Basic validation
    if (!$product_id || $rating === false || $rating < 1 || $rating > 5 || empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input. Please provide a product, rating (1-5), and a comment.']);
        exit();
    }

    // Determine user_id or session_id and user_name
    $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    $session_id = session_id(); // Use session_id for guests
    $user_name = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : 'Anonymous'; // Assuming user name is in session

    // Prepare an SQL statement for insertion
    // The 'created_at' column in the database table has a DEFAULT of current_timestamp(), so it doesn't need to be included in the INSERT statement
    $sql = "INSERT INTO reviews (product_id, user_id, session_id, user_name, rating, comment) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);

    if ($stmt) {
        // Bind parameters: 'i' for integer, 's' for string. user_id can be NULL so it needs special handling if not using bind_param_array
        // For user_id, if it's null, we pass null directly to bind_param.
        $stmt->bind_param("iisiss", $product_id, $user_id, $session_id, $user_name, $rating, $comment);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Review submitted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit review: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $con->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$con->close();
