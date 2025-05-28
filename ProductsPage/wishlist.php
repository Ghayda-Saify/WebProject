<?php
global $con;
session_start();
require '../connection.php'; // Adjust path if needed

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
$session_id = session_id(); // Get current session ID for guest carts/wishlists

// --- Fetch Wishlist Items for Display ---
$wishlist_items_data = [];
$sql_wishlist = "SELECT p.id, p.name, p.price, p.short_desc, p.image
                 FROM wishlist w
                 JOIN product p ON w.product_id = p.id
                 WHERE ";

$params = [];
$types = "";

if ($user_id) {
    $sql_wishlist .= "w.user_id = ?";
    $params[] = $user_id;
    $types .= "i";
} else {
    // If no user_id, check for session_id
    $sql_wishlist .= "w.session_id = ?";
    $params[] = $session_id;
    $types .= "s";
}

$stmt_wishlist = $con->prepare($sql_wishlist);
if ($stmt_wishlist === false) {
    die('Wishlist Prepare failed: ' . $con->error); // Added for debugging prepare errors
}

if (!empty($params)) {
    // FIX FOR bind_param: Create references for dynamic binding
    $bind_names = [];
    foreach ($params as $key => $value) {
        $bind_name = "bind" . $key;
        $$bind_name = $value; // Create a variable variable
        $bind_names[] = &$$bind_name; // Store a reference to it
    }
    array_unshift($bind_names, $types); // Add the type string at the beginning

    // Call bind_param with the array of references
    call_user_func_array([$stmt_wishlist, 'bind_param'], $bind_names); // THIS IS LINE 37
}
$stmt_wishlist->execute();
$result_wishlist = $stmt_wishlist->get_result();

while ($row = $result_wishlist->fetch_assoc()) {
    $wishlist_items_data[] = $row;
}
$stmt_wishlist->close();

// --- Fetch Cart and Wishlist Counts for Header ---
$cart_count = 0;
if ($user_id) {
    $cart_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $cart_stmt = $con->prepare($cart_sql);
    if ($cart_stmt === false) { die('Cart Prepare failed: ' . $con->error); }
    $cart_stmt->bind_param("i", $user_id);
} else {
    $cart_sql = "SELECT SUM(quantity) as total FROM cart WHERE session_id = ?";
    $cart_stmt = $con->prepare($cart_sql);
    if ($cart_stmt === false) { die('Cart Prepare failed: ' . $con->error); }
    $cart_stmt->bind_param("s", $session_id);
}
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
if ($row = $cart_result->fetch_assoc()) {
    $cart_count = $row['total'] ?? 0;
}
$cart_stmt->close();

// Wishlist count is directly derived from the fetched items for display on this page
$wishlist_count = count($wishlist_items_data);

$con->close(); // Close connection after all queries are done
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alandalus Design | Wishlist</title>
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
            <li><a href="product.php">Products</a></li>
            <li><a href="../ContactPage/contact.php">Connect</a></li>
            <li>
                <a href="../CartPage/cart.php" class="relative">
                    <i class="fa-solid fa-cart-shopping text-primary"></i>
                    <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count"><?php echo $cart_count; ?></span>
                </a>
            </li>
            <li>
                <?php $profileLink = isset($_SESSION['user']['id']) ? '../ProfilePage/profile.php' : '../SignIn&Up/sign.php'; // Corrected link ?>
                <a href="<?php echo $profileLink; ?>">
                    <i class="fa-solid fa-user text-primary"></i>
                </a>
            </li>
            <li>
                <a href="wishlist.php" class="relative text-primary font-bold">
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
                    <span class="text-primary font-medium">Wishlist</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">My Wishlist</h1>

        <div id="empty-wishlist" class="text-center py-12 hidden">
            <i class="far fa-heart text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 mb-4">Your wishlist is empty</p>
            <a href="product.php" class="inline-block bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition">
                Browse Products
            </a>
        </div>

        <div id="wishlist-items" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
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
                    <li><a href="product.php" class="text-gray-600 hover:text-primary transition">Products</a></li>
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
    <span></span>
</div>

<script>
    // Data passed from PHP
    let wishlistItemsData = <?php echo json_encode($wishlist_items_data); ?>;
    const cartCountSpan = document.querySelector('.cart-count');
    const wishlistCountSpan = document.querySelector('.wishlist-count');
    const wishlistItemsContainer = document.getElementById('wishlist-items');
    const emptyWishlistMessage = document.getElementById('empty-wishlist');
    const successToast = document.getElementById('success-toast');

    // Function to show toast notification
    function showToast(message, isSuccess = true) {
        successToast.classList.remove(isSuccess ? 'bg-red-500' : 'bg-green-500');
        successToast.classList.add(isSuccess ? 'bg-green-500' : 'bg-red-500');
        successToast.querySelector('span').textContent = message;
        successToast.classList.remove('translate-y-full', 'opacity-0');
        setTimeout(() => {
            successToast.classList.add('translate-y-full', 'opacity-0');
        }, 3000);
    }

    // Update counts (initial load from PHP)
    // These lines are already correct if PHP is echoing the counts
    // cartCountSpan.textContent = <?php echo $cart_count; ?>;
    // wishlistCountSpan.textContent = <?php echo $wishlist_count; ?>;

    // Function to remove item from wishlist via AJAX
    function removeFromWishlist(productId) {
        fetch('remove_from_wishlist.php', { // Corrected: Calls remove_from_wishlist.php
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: product_id=${productId}
    })
    .then(response => response.json())
            .then(data => {
                showToast(data.message, data.success);
                if (data.success) { // Simpler check for success
                    // Remove item from local data and re-render
                    wishlistItemsData = wishlistItemsData.filter(item => item.id !== productId);
                    renderWishlist();
                    // Update wishlist count in header from the server's response
                    wishlistCountSpan.textContent = data.wishlist_count;
                }
            })
            .catch(error => {
                console.error('Error removing from wishlist:', error);
                showToast('An error occurred while removing from wishlist', false);
            });
    }

    // Function to add item to cart via AJAX (with default size for simplicity from wishlist page)
    function addToCartFromWishlist(productId) {
        const quantity = 1; // Default quantity when adding from wishlist page
        const size = 'medium'; // Default size when adding from wishlist page (adjust as needed)

        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}&quantity=${quantity}&size=${size}`    })
    .then(response => response.json())
            .then(data => {
                showToast(data.message, data.success);
                if (data.success) {
                    // Update cart count in header
                    cartCountSpan.textContent = data.cart_count;
                    // Optionally remove from wishlist after adding to cart if desired
                    // removeFromWishlist(productId);
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showToast('An error occurred while adding to cart', false);
            });
    }

    // Render wishlist items from server data
    function renderWishlist() {
        if (wishlistItemsData.length === 0) {
            emptyWishlistMessage.classList.remove('hidden');
            wishlistItemsContainer.classList.add('hidden');
        } else {
            emptyWishlistMessage.classList.add('hidden');
            wishlistItemsContainer.classList.remove('hidden');
            wishlistItemsContainer.innerHTML = wishlistItemsData.map(item => `
                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all">
                        <div class="h-64 overflow-hidden relative">
                            <img src="../HomePage/imgs/${item.image}" alt="${item.name}" class="w-full h-full object-contain">
                        </div>
                        <div class="p-6">
                            <h3 class="font-aboreto text-xl mb-2">${item.name}</h3>
                            <p class="text-primary font-aboreto text-lg mb-4">₪ ${parseFloat(item.price).toFixed(2)}</p>
                            <div class="flex justify-between items-center">
                                <button onclick="addToCartFromWishlist(${item.id})"
                                        class="bg-primary text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition">
                                    Add to Cart
                                </button>
                                <button onclick="removeFromWishlist(${item.id})"
                                        class="text-red-500 hover:text-red-600 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
        }
    }

    // Initial render on page load
    document.addEventListener('DOMContentLoaded', renderWishlist);

</script>
</body>
</html>