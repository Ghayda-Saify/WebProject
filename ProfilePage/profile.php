<?php
session_start();
require_once '../connection.php';
global $con;

// --- PHP Logic for AJAX requests and initial page load ---

$user = null;
$user_id = null;

// Use $_SESSION['user']['id'] for primary check if possible, or fallback to email
if (isset($_SESSION['user']['id'])) {
    $user_id = $_SESSION['user']['id'];
    $stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // If user data not found by ID (e.g., deleted account), destroy session
    if (!$user) {
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session
        header("Location: ../SignIn&Up/sign.php");
        exit();
    }
    // Ensure $_SESSION['user_email'] is set for consistency with other parts of the site
    // (though $_SESSION['user']['email'] is generally preferred if the whole user object is stored)
    $_SESSION['user_email'] = $user['email'];

} else if (isset($_SESSION['user_email'])) { // Fallback if only email is in session
    $email = $_SESSION['user_email'];
    $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    if ($user) {
        $user_id = $user['id'];
        // Update session with full user array for consistency
        $_SESSION['user'] = $user;
    } else {
        // User email in session but no user found in DB, clear session
        session_unset();
        session_destroy();
        header("Location: ../SignIn&Up/sign.php");
        exit();
    }
} else {
    // If user is not logged in (neither user_id nor user_email in session), redirect
    header("Location: ../SignIn&Up/sign.php");
    exit();
}


// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];

    // Ensure user is logged in for AJAX actions
    if (!$user_id) {
        $response['message'] = 'Authentication required.';
        echo json_encode($response);
        $con->close();
        exit();
    }

    if ($_GET['action'] === 'load_orders') {
        $orders = [];
        // Fetch orders for the logged-in user
        $sql_orders = "SELECT o.order_id, o.total_price, o.created_at, o.status
                       FROM orders o
                       WHERE o.user_id = ?
                       ORDER BY o.created_at DESC";
        $stmt_orders = $con->prepare($sql_orders);
        $stmt_orders->bind_param("i", $user_id);
        $stmt_orders->execute();
        $result_orders = $stmt_orders->get_result();

        while ($order = $result_orders->fetch_assoc()) {
            $order_items = [];
            // Fetch items for each order
            $sql_items = "SELECT oi.quantity, oi.price, p.name as product_name, p.image as product_image
                          FROM order_items oi
                          JOIN product p ON oi.product_id = p.id
                          WHERE oi.order_id = ?";
            $stmt_items = $con->prepare($sql_items);
            $stmt_items->bind_param("i", $order['order_id']);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();
            while ($item = $result_items->fetch_assoc()) {
                $order_items[] = $item;
            }
            $stmt_items->close();
            $order['items'] = $order_items;
            $orders[] = $order;
        }
        $stmt_orders->close();
        $response['success'] = true;
        $response['orders'] = $orders;
    } else if ($_GET['action'] === 'change_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_new_password = $_POST['confirm_new_password'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
            $response['message'] = 'All password fields are required.';
        } elseif ($new_password !== $confirm_new_password) {
            $response['message'] = 'New password and confirmation do not match.';
        } elseif (strlen($new_password) < 6) { // Example: minimum password length
            $response['message'] = 'New password must be at least 6 characters long.';
        } else {
            // Verify current password
            $hashed_current_password = sha1($current_password); // Using SHA1 as per your users table
            if ($hashed_current_password === $user['password']) { // Use $user from top of script
                $hashed_new_password = sha1($new_password);
                $sql_update_pass = "UPDATE users SET password = ? WHERE id = ?";
                $stmt_update_pass = $con->prepare($sql_update_pass);
                $stmt_update_pass->bind_param("si", $hashed_new_password, $user_id);
                if ($stmt_update_pass->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Password updated successfully!';
                } else {
                    $response['message'] = 'Failed to update password: ' . $con->error;
                }
                $stmt_update_pass->close();
            } else {
                $response['message'] = 'Current password is incorrect.';
            }
        }
    } else if ($_GET['action'] === 'update_notifications' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $email_orders_noti = isset($_POST['email_orders_noti']) ? 1 : 0;
        $email_promo_noti = isset($_POST['email_promo_noti']) ? 1 : 0;

        $sql_update_noti = "UPDATE users SET email_orders_noti = ?, email_promo_noti = ? WHERE id = ?";
        $stmt_update_noti = $con->prepare($sql_update_noti);
        $stmt_update_noti->bind_param("iii", $email_orders_noti, $email_promo_noti, $user_id);
        if ($stmt_update_noti->execute()) {
            $response['success'] = true;
            $response['message'] = 'Notification preferences updated!';
            // Update the $user array for the current page load, and potentially $_SESSION['user']
            $user['email_orders_noti'] = $email_orders_noti;
            $user['email_promo_noti'] = $email_promo_noti;
            if (isset($_SESSION['user'])) {
                $_SESSION['user']['email_orders_noti'] = $email_orders_noti;
                $_SESSION['user']['email_promo_noti'] = $email_promo_noti;
            }
        } else {
            $response['message'] = 'Failed to update notification preferences: ' . $con->error;
        }
        $stmt_update_noti->close();
    } else if ($_GET['action'] === 'delete_account' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // In a real application, you'd ask for password confirmation
        // and carefully handle data deletion (e.g., soft delete,
        // transfer ownership of orders, etc.)
        // Ensure foreign key constraints are set up with ON DELETE CASCADE
        // or delete from dependent tables (cart, wishlist, orders, order_items) first.

        // Example: Delete from cart and wishlist first (if not cascading)
        $con->begin_transaction();
        try {
            $stmt_del_cart = $con->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt_del_cart->bind_param("i", $user_id);
            $stmt_del_cart->execute();
            $stmt_del_cart->close();

            $stmt_del_wishlist = $con->prepare("DELETE FROM wishlist WHERE user_id = ?");
            $stmt_del_wishlist->bind_param("i", $user_id);
            $stmt_del_wishlist->execute();
            $stmt_del_wishlist->close();

            // Then delete the user
            $sql_delete_user = "DELETE FROM users WHERE id = ?";
            $stmt_delete_user = $con->prepare($sql_delete_user);
            $stmt_delete_user->bind_param("i", $user_id);
            if ($stmt_delete_user->execute()) {
                $con->commit();
                session_unset(); // Unset all session variables
                session_destroy(); // Destroy the session
                $response['success'] = true;
                $response['message'] = 'Your account has been deleted successfully.';
                $response['redirect'] = '../SignIn&Up/sign.php'; // Redirect after deletion
            } else {
                $con->rollback();
                $response['message'] = 'Failed to delete user: ' . $con->error;
            }
            $stmt_delete_user->close();
        } catch (mysqli_sql_exception $e) {
            $con->rollback();
            $response['message'] = 'Database error during account deletion: ' . $e->getMessage();
        }

    }


    echo json_encode($response);
    $con->close();
    exit(); // IMPORTANT: Exit after AJAX response
}

// --- PHP Logic for Cart and Wishlist Counts (for header) ---
$cart_count = 0;
$session_id = session_id(); // Always get session_id for guest carts/wishlists

if ($user_id) { // If a logged-in user
    $cart_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $cart_stmt = $con->prepare($cart_sql);
    $cart_stmt->bind_param("i", $user_id);
} else { // If a guest user
    $cart_sql = "SELECT SUM(quantity) as total FROM cart WHERE session_id = ?";
    $cart_stmt = $con->prepare($cart_sql);
    $cart_stmt->bind_param("s", $session_id);
}
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
if ($row = $cart_result->fetch_assoc()) {
    $cart_count = $row['total'] ?? 0;
}
$cart_stmt->close();

$wishlist_count = 0;
if ($user_id) { // If a logged-in user
    $wishlist_sql = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?";
    $wishlist_stmt = $con->prepare($wishlist_sql);
    $wishlist_stmt->bind_param("i", $user_id);
} else { // If a guest user
    $wishlist_sql = "SELECT COUNT(*) as total FROM wishlist WHERE session_id = ?";
    $wishlist_stmt = $con->prepare($wishlist_sql);
    $wishlist_stmt->bind_param("s", $session_id);
}
$wishlist_stmt->execute();
$wishlist_result = $wishlist_stmt->get_result();
if ($row = $wishlist_result->fetch_assoc()) {
    $wishlist_count = $row['total'] ?? 0;
}
$wishlist_stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alandalus Design | Profile</title>
    <link rel="stylesheet" href="../HomePage/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#122c6f",
                        secondary: "#f13b1c",
                        beige: "#F5F5DC",
                        gold: "#FFD700"
                    }
                },
            },
        };
    </script>
</head>
<body class="font-poppins bg-beige/10">
<header>
    <a href="../HomePage/index.php" class="logo text-primary font-['Pacifico'] text-2xl">Alandalus Design</a>
    <nav class="main-nav">
        <ul>
            <li><a href="../HomePage/index.php">Home</a></li>
            <li><a href="../ProductsPage/product.php">Products</a></li>
            <li><a href="../ContactPage/contact.php">Connect</a></li>
            <li>
                <a href="../CartPage/cart.php" class="relative">
                    <i class="fa-solid fa-cart-shopping text-primary"></i>
                    <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count"><?php echo $cart_count; ?></span>
                </a>
            </li>
            <li>
                <?php $profileLink = isset($_SESSION['user']['id']) ? 'profile.php' : '../SignIn&Up/sign.php'; // Corrected check and link ?>
                <a href="<?php echo $profileLink; ?>" class="text-primary font-bold">
                    <i class="fa-solid fa-user text-primary"></i>
                </a>
            </li>
            <li>
                <a href="../ProductsPage/wishlist.php" class="relative">
                    <i class="fa-solid fa-heart text-primary"></i>
                    <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center wishlist-count"><?php echo $wishlist_count; ?></span>
                </a>
            </li>
        </ul>
    </nav>
</header>

<main class="container mx-auto px-4 py-8">
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="../HomePage/index.php" class="text-gray-700 hover:text-primary">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-primary font-medium">Profile</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-primary/10 mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-user text-4xl text-primary"></i>
                        </div>
                        <h2 class="text-xl font-semibold" id="user-name"><?php echo htmlspecialchars($user['name'] ?? ''); ?></h2>
                        <p class="text-gray-600 text-sm" id="user-email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                    </div>
                    <nav class="space-y-2">
                        <button class="w-full text-left px-4 py-2 rounded-lg hover:bg-primary/5 transition tab-button active" data-tab="profile">
                            <i class="fas fa-user-circle mr-2"></i> Profile
                        </button>
                        <button class="w-full text-left px-4 py-2 rounded-lg hover:bg-primary/5 transition tab-button" data-tab="orders">
                            <i class="fas fa-shopping-bag mr-2"></i> Orders
                        </button>
                        <button class="w-full text-left px-4 py-2 rounded-lg hover:bg-primary/5 transition tab-button" data-tab="settings">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </button>
                        <button>    <a href="logout.php" class="w-full text-left px-4 py-2 rounded-lg hover:bg-primary/5 transition text-red-500" id="logout-button">
                                <i class="fas fa-sign-out-alt mr-2" id="logout"></i> Logout
                            </a></button>
                    </nav>
                </div>
            </div>

            <div class="md:col-span-3">
                <div class="tab-content active" id="profile-tab">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-xl font-semibold mb-6">Personal Information</h3>
                        <form id="profile-form" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Name</label>
                                    <input type="text" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-primary/30 focus:border-primary" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Type</label>
                                    <input type="text" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-primary/30 focus:border-primary" value="<?php echo htmlspecialchars($user['type'] ?? ''); ?>" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Email</label>
                                <input type="email" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-primary/30 focus:border-primary" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Phone</label>
                                <input type="tel" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-primary/30 focus:border-primary" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" readonly>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="tab-content hidden" id="orders-tab">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-xl font-semibold mb-6">Order History</h3>
                        <div id="orders-list" class="space-y-4">
                            <p class="text-gray-500 text-center">Loading orders...</p>
                        </div>
                    </div>
                </div>

                <div class="tab-content hidden" id="settings-tab">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-xl font-semibold mb-6">Account Settings</h3>
                        <div class="space-y-6">
                            <div class="pb-6 border-b">
                                <h4 class="font-medium mb-4">Change Password</h4>
                                <form id="password-form" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1" for="current_password">Current Password</label>
                                        <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-primary/30 focus:border-primary" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1" for="new_password">New Password</label>
                                        <input type="password" id="new_password" name="new_password" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-primary/30 focus:border-primary" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1" for="confirm_new_password">Confirm New Password</label>
                                        <input type="password" id="confirm_new_password" name="confirm_new_password" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-primary/30 focus:border-primary" required>
                                    </div>
                                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition">
                                        Update Password
                                    </button>
                                </form>
                            </div>

                            <div class="pb-6 border-b">
                                <h4 class="font-medium mb-4">Notification Preferences</h4>
                                <form id="notifications-form" class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="email_orders_noti" name="email_orders_noti" class="form-checkbox text-primary rounded" <?php echo (($user['email_orders_noti'] ?? 1) == 1) ? 'checked' : ''; ?>>
                                        <span class="ml-2">Email notifications for orders</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" id="email_promo_noti" name="email_promo_noti" class="form-checkbox text-primary rounded" <?php echo (($user['email_promo_noti'] ?? 1) == 1) ? 'checked' : ''; ?>>
                                        <span class="ml-2">Email notifications for promotions</span>
                                    </label>
                                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition">
                                        Save Preferences
                                    </button>
                                </form>
                            </div>

                            <div>
                                <h4 class="font-medium text-red-500 mb-4">Delete Account</h4>
                                <p class="text-sm text-gray-600 mb-4">
                                    Once you delete your account, there is no going back. Please be certain.
                                </p>
                                <button id="delete-account-button" class="text-red-500 border border-red-500 px-6 py-2 rounded-full hover:bg-red-50 transition">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="bg-gray-100 mt-16">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <a href="../HomePage/index.php" class="text-primary font-['Pacifico'] text-2xl">Alandalus Design</a>
                <p class="mt-4 text-gray-600">Crafting personalized Arabic and Islamic designs that tell your unique story. Every piece is created with love and attention to detail.</p>
                <div class="mt-6 flex space-x-4">
                    <a href="https://www.facebook.com/Al.Andalus.Design" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-secondary transition" title="Follow us on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/andalus_design" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-secondary transition" title="Follow us on Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://t.me/andalusdesign" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-secondary transition" title="Join our Telegram Channel">
                        <i class="fab fa-telegram"></i>
                    </a>
                    <a href="https://wa.me/message/7YZUEAMKO53SM1" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-secondary transition" title="Contact us on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="../HomePage/index.php" class="text-gray-600 hover:text-primary transition">Home</a></li>
                    <li><a href="../ProductsPage/product.php" class="text-gray-600 hover:text-primary transition">Products</a></li>
                    <li><a href="../ContactPage/contact.php" class="text-gray-600 hover:text-primary transition">Contact</a></li>
                    <li><a href="../CartPage/cart.php" class="text-gray-600 hover:text-primary transition">Cart</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-4">Contact Us</h3>
                <ul class="space-y-2">
                    <li class="flex items-center text-gray-600">
                        <i class="fas fa-envelope w-6"></i>
                        <span>infoalandalusdesign@gmail.com</span>
                    </li>
                    <li class="flex items-center text-gray-600">
                        <i class="fas fa-phone w-6"></i>
                        <span>+972 59-464-6503</span>
                    </li>
                    <li class="flex items-center text-gray-600">
                        <i class="fas fa-map-marker-alt w-6"></i>
                        <span>Sufyan Street - Raddad Building, Nablus 009709</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-200 mt-8 pt-8 text-center text-gray-600">
            <p>&copy; 2024 Alandalus Design. All rights reserved.</p>
        </div>
    </div>
</footer>

<div id="success-toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 flex items-center">
    <i class="fas fa-check-circle mr-2"></i>
    <span id="toast-message">Changes saved successfully!</span>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Header Cart & Wishlist Counts (PHP now handles this on page load) ---
        // These are now populated by PHP on initial page load, so no need for client-side localStorage update here.
        // const cart = JSON.parse(localStorage.getItem('cart')) || [];
        // const cartCount = document.querySelector('.cart-count');
        // cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);


        // --- Tab switching functionality ---
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        const ordersList = document.getElementById('orders-list');
        const passwordForm = document.getElementById('password-form');
        const notificationsForm = document.getElementById('notifications-form');
        const deleteAccountButton = document.getElementById('delete-account-button');
        const toast = document.getElementById('success-toast');
        const toastMessage = document.getElementById('toast-message');

        function showToast(message, isSuccess = true) {
            toastMessage.textContent = message;
            if (isSuccess) {
                toast.classList.remove('bg-red-500');
                toast.classList.add('bg-green-500');
            } else {
                toast.classList.remove('bg-green-500');
                toast.classList.add('bg-red-500');
            }
            toast.classList.remove('translate-y-full', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-full', 'opacity-0');
            }, 3000);
        }

        // Function to load orders
        function loadOrders() {
            ordersList.innerHTML = '<p class="text-gray-500 text-center">Loading orders...</p>';
            fetch('profile.php?action=load_orders')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.orders.length > 0) {
                        ordersList.innerHTML = ''; // Clear loading message
                        data.orders.forEach(order => {
                            const orderDiv = document.createElement('div');
                            orderDiv.className = 'border rounded-lg p-4';
                            orderDiv.innerHTML = `
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="font-medium">Order #${order.order_id}</p>
                                        <p class="text-sm text-gray-500">Placed on ${new Date(order.created_at).toLocaleDateString()}</p>
                                    </div>
                                    <span class="px-3 py-1 <span class="math-inline">\{order\.status \=\=\= 'Delivered' ? 'bg\-green\-100 text\-green\-700' \: \(order\.status \=\=\= 'Processing' ? 'bg\-blue\-100 text\-blue\-700' \: 'bg\-gray\-100 text\-gray\-700'\)\} rounded\-full text\-sm"\></span>{order.status}</span>
                                </div>
                                <div class="space-y-2">
                                    ${order.items.map(item => `
                                        <div class="flex items-center">
                                            <img src="../HomePage/imgs/${item.product_image}" alt="${item.product_name}" class="w-16 h-16 object-contain">
                                            <div class="ml-4">
                                                <p class="font-medium">${item.product_name}</p>
                                                <p class="text-sm text-gray-500">Quantity: ${item.quantity}</p>
                                                <p class="text-primary">₪${parseFloat(item.price).toFixed(2)}</p>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                                <div class="mt-4 pt-4 border-t flex justify-between items-center">
                                    <p class="font-medium">Total: ₪<span class="math-inline">\{parseFloat\(order\.total\_price\)\.toFixed\(2\)\}</p\>
<button class\="text\-primary hover\:text\-opacity\-80 transition" onclick\="alert\('Order details for \#</span>{order.order_id}')">
                                        View Details
                                    </button>
                                </div>
                            `;
                            ordersList.appendChild(orderDiv);
                        });
                    } else if (data.success && data.orders.length === 0) {
                        ordersList.innerHTML = '<p class="text-gray-500 text-center">No orders found.</p>';
                    } else {
                        ordersList.innerHTML = `<p class="text-red-500 text-center">Error loading orders: ${data.message}</p>`;
                        showToast(`Error loading orders: ${data.message}`, false);
                    }
                })
                .catch(error => {
                    console.error('Error fetching orders:', error);
                    ordersList.innerHTML = '<p class="text-red-500 text-center">An error occurred while fetching orders.</p>';
                    showToast('An error occurred while fetching orders.', false);
                });
        }


        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabName = button.dataset.tab;

                // Update active states
                tabButtons.forEach(btn => btn.classList.remove('active', 'bg-primary/5', 'text-primary'));
                button.classList.add('active', 'bg-primary/5', 'text-primary');

                // Show selected tab content
                tabContents.forEach(content => {
                    if (content.id === `${tabName}-tab`) {
                        content.classList.remove('hidden');
                        if (tabName === 'orders') {
                            loadOrders(); // Load orders when orders tab is active
                        }
                    } else {
                        content.classList.add('hidden');
                    }
                });
            });
        });

        // --- Password Change Form Submission ---
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(passwordForm);
            formData.append('action', 'change_password');

            fetch('profile.php?action=change_password', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    showToast(data.message, data.success);
                    if (data.success) {
                        passwordForm.reset(); // Clear form fields
                    }
                })
                .catch(error => {
                    console.error('Error changing password:', error);
                    showToast('An error occurred while changing password.', false);
                });
        });

        // --- Notification Preferences Form Submission ---
        notificationsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(notificationsForm);
            formData.append('email_orders_noti', document.getElementById('email_orders_noti').checked ? '1' : '0');
            formData.append('email_promo_noti', document.getElementById('email_promo_noti').checked ? '1' : '0');
            formData.append('action', 'update_notifications');

            fetch('profile.php?action=update_notifications', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    showToast(data.message, data.success);
                })
                .catch(error => {
                    console.error('Error updating notifications:', error);
                    showToast('An error occurred while updating notification preferences.', false);
                });
        });

        // --- Delete Account Button ---
        deleteAccountButton.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('action', 'delete_account');

                fetch('profile.php?action=delete_account', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        showToast(data.message, data.success);
                        if (data.success && data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 2000); // Redirect after toast
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting account:', error);
                        showToast('An error occurred while deleting your account.', false);
                    });
            }
        });
    });
</script>
</body>
</html>