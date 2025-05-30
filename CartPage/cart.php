<?php
global $con;
session_start();
include '../connection.php'; // Ensure this file establishes the $con variable

if (!isset($_SESSION['user'])) {
    header('Location: ../SignIn&Up/sign.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch cart items for the logged-in user
$sql = "
  SELECT 
    cart.id AS cart_id,
    product.id AS product_id,
    product.name,
    product.price,
    product.image,
    cart.quantity
  FROM cart
  JOIN product ON cart.product_id = product.id
  WHERE cart.user_id = $user_id
";

$result = $con->query($sql);
$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

// Calculate subtotal
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 20.00; // Fixed shipping cost
$total = $subtotal + $shipping;

// Fetch cart count
//$session_id = session_id(); // This was problematic, using session_id() directly for anonymous carts without proper management can be insecure or inconsistent
$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
$cart_count = 0;
// Using prepared statements for security against SQL injection
if ($user_id) {
    $cart_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $cart_stmt = $con->prepare($cart_sql);
    $cart_stmt->bind_param("i", $user_id); // 'i' for integer user_id
} else {
    $session_id = session_id(); // Use session_id for guests
    $cart_sql = "SELECT SUM(quantity) as total FROM cart WHERE session_id = ?";
    $cart_stmt = $con->prepare($cart_sql);
    $cart_stmt->bind_param("s", $session_id); // 's' for string session_id
}
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
if ($row = $cart_result->fetch_assoc()) {
    $cart_count = $row['total'] ?? 0;
}
$cart_stmt->close();

// Fetch wishlist count
$wishlist_count = 0;
// Using prepared statements for security against SQL injection
if ($user_id) {
    $wishlist_sql = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?";
    $wishlist_stmt = $con->prepare($wishlist_sql);
    $wishlist_stmt->bind_param("i", $user_id); // 'i' for integer user_id
} else {
    $session_id = session_id(); // Use session_id for guests
    $wishlist_sql = "SELECT COUNT(*) as total FROM wishlist WHERE session_id = ?";
    $wishlist_stmt = $con->prepare($wishlist_sql);
    $wishlist_stmt->bind_param("s", $session_id); // 's' for string session_id
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
    <title>Alandalus Design | Cart</title>
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
                    colors: { primary: "#122c6f", secondary: "#f13b1c" }
                },
            },
        };
    </script>
</head>
<body class="font-poppins">
    <header>
        <a href="../HomePage/index.php" class="logo text-primary font-['Pacifico'] text-2xl">Alandalus Design</a>
        <!-- Navigation Bar -->
        <nav class="main-nav">
            <ul>
                <li><a href="../HomePage/index.php">Home</a></li>
                <li><a href="../ProductsPage/product.php">Products</a></li>
                <li><a href="../ContactPage/contact.php">Connect</a></li>
                <li>
                    <a href="cart.php" class="relative">
                        <i class="fa-solid fa-cart-shopping text-primary"></i>
                        <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count"><?php echo $cart_count; ?></span>
                    </a>
                </li>
                <li>
                    <a href="../ProfilePage/profile.php">
                        <i class="fa-solid fa-user text-primary"></i>
                    </a>
                </li>
                <li>
                    <a href="../ProductsPage/wishlist.php" class="relative" id="wishlist-icon">
                        <i class="fa-solid fa-heart text-primary"></i>
                        <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center wishlist-count"><?php echo $wishlist_count; ?></span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <div class="text-sm text-gray-500 mb-8">
            <a href="../HomePage/index.php" class="hover:text-blue-600">Home</a>
            <span class="mx-2">/</span>
            <a href="../ProductsPage/product.php" class="hover:text-blue-600">Products</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800">Shopping Cart</span>
        </div>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items -->
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-semibold mb-6">Shopping Cart</h2>
                        <?php if (count($cart_items) > 0): ?>
                            <?php foreach ($cart_items as $item): ?>
                                <div class="flex items-center justify-between border-b pb-4 mb-4">
                                    <div class="flex items-center">
                                        <img src="../HomePage/imgs/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-20 h-20 object-cover rounded">
                                        <div class="ml-4">
                                            <h4 class="text-lg font-semibold"><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <p class="text-gray-500">Price: ₪<?php echo number_format($item['price'], 2); ?></p>
                                            <p class="text-gray-500">Quantity: <?php echo $item['quantity']; ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-gray-700 font-semibold">₪<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                        <form method="post" action="delete_cart_item.php">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <button type="submit" class="text-red-500 hover:text-red-700 mt-2">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-gray-500">Your cart is empty. <a href="../ProductsPage/product.php" class="text-blue-600 hover:underline">Continue shopping</a></p>
                        <?php endif; ?>
                    </div>
                </div>


                <!-- Order Summary -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>₪<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span>₪<?php echo number_format($shipping, 2); ?></span>
                        </div>
                        <div class="border-t pt-3 mt-3">
                            <div class="flex justify-between font-semibold">
                                <span>Total</span>
                                <span>₪<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>
                    <form method="post" action="../CheckoutPage/checkout.php">
                        <button  type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg mt-6 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Proceed to Checkout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Section -->
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

                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="../HomePage/index.php" class="text-gray-600 hover:text-primary transition">Home</a></li>
                        <li><a href="../ProductsPage/product.php" class="text-gray-600 hover:text-primary transition">Products</a></li>
                        <li><a href="../ContactPage/contact.php" class="text-gray-600 hover:text-primary transition">Contact</a></li>
                        <li><a href="cart.html" class="text-gray-600 hover:text-primary transition">Cart</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
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

            <!-- Copyright -->
            <div class="border-t border-gray-200 mt-8 pt-8 text-center text-gray-600">
                <p>&copy; 2024 Alandalus Design. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Success Toast -->
    <div id="success-toast" class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 flex items-center">
        <i class="fas fa-trash mr-2"></i>
        <span>Item removed from cart!</span>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
            <div class="text-center mb-6">
                <i class="fas fa-trash-alt text-red-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Delete Item</h3>
                <p class="text-gray-600">Are you sure you want to delete this item from your cart?</p>
            </div>
            <div class="flex justify-center space-x-4">
                <button id="cancel-remove" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button id="confirm-remove" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition flex items-center">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>

    <script src="cart.js"></script>
    <script>
        // DATABASE TODO: Replace localStorage with database operations for wishlist management
        // document.addEventListener('DOMContentLoaded', function() {
        //     const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
        //     const wishlistCount = document.querySelector('.wishlist-count');
        //     if (wishlistCount) {
        //         wishlistCount.textContent = wishlist.length;
        //     }
        // });
    </script>
</body>
</html>