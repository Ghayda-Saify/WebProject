<?php
session_start();
require_once '../connection.php';
global $con;

$user = null;
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];
    $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
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
    <!-- Navigation Bar -->
    <nav class="main-nav">
        <ul>
            <li><a href="../HomePage/index.php">Home</a></li>
            <li><a href="../ProductsPage/product.php">Products</a></li>
            <li><a href="../ContactPage/contact.html">Connect</a></li>
            <li>
                <a href="../CartPage/cart.html" class="relative">
                    <i class="fa-solid fa-cart-shopping text-primary"></i>
                    <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count">0</span>
                </a>
            </li>
            <li>
                <?php $profileLink = isset($_SESSION['user_email']) ? 'profile.html' : '../SignIn&Up/sign.php'; ?>
                <a href="<?php echo $profileLink; ?>" class="text-primary font-bold">
                    <i class="fa-solid fa-user text-primary"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<main class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
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

    <!-- Profile Content -->
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Sidebar -->
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
                        <a href="../SignIn&Up/sign.php"><button class="w-full text-left px-4 py-2 rounded-lg hover:bg-primary/5 transition text-red-500" id="logout-button">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button></a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="md:col-span-3">
                <!-- Profile Tab -->
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

                <!-- Orders Tab -->
                <div class="tab-content hidden" id="orders-tab">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-xl font-semibold mb-6">Order History</h3>
                        <!-- DATABASE TODO: Replace with actual order data from database -->
                        <div class="space-y-4">
                            <!-- Sample Order -->
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="font-medium">Order #12345</p>
                                        <p class="text-sm text-gray-500">Placed on March 15, 2024</p>
                                    </div>
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Delivered</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <img src="../HomePage/imgs/notebook2-removebg-preview.png" alt="Product" class="w-16 h-16 object-contain">
                                        <div class="ml-4">
                                            <p class="font-medium">Custom Notebook</p>
                                            <p class="text-sm text-gray-500">Quantity: 1</p>
                                            <p class="text-primary">₪20.00</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t flex justify-between items-center">
                                    <p class="font-medium">Total: ₪20.00</p>
                                    <button class="text-primary hover:text-opacity-80 transition">
                                        View Details
                                    </button>
                                </div>
                            </div>

                            <!-- Sample Order 2 -->
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="font-medium">Order #12344</p>
                                        <p class="text-sm text-gray-500">Placed on March 10, 2024</p>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">Processing</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <img src="../HomePage/imgs/hoodi-removebg-preview.png" alt="Product" class="w-16 h-16 object-contain">
                                        <div class="ml-4">
                                            <p class="font-medium">Custom Hoodie</p>
                                            <p class="text-sm text-gray-500">Quantity: 1</p>
                                            <p class="text-primary">₪60.00</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t flex justify-between items-center">
                                    <p class="font-medium">Total: ₪60.00</p>
                                    <button class="text-primary hover:text-opacity-80 transition">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-content hidden" id="settings-tab">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-xl font-semibold mb-6">Account Settings</h3>
                        <!-- DATABASE TODO: Replace with actual user settings from database -->
                        <div class="space-y-6">
                            <!-- Password Change -->
                            <div class="pb-6 border-b">
                                <h4 class="font-medium mb-4">Change Password</h4>
                                <form class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Current Password</label>
                                        <input type="password" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">New Password</label>
                                        <input type="password" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Confirm New Password</label>
                                        <input type="password" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    </div>
                                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition">
                                        Update Password
                                    </button>
                                </form>
                            </div>

                            <!-- Notifications -->
                            <div class="pb-6 border-b">
                                <h4 class="font-medium mb-4">Notification Preferences</h4>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="form-checkbox text-primary rounded" checked>
                                        <span class="ml-2">Email notifications for orders</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="form-checkbox text-primary rounded" checked>
                                        <span class="ml-2">Email notifications for promotions</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Delete Account -->
                            <div>
                                <h4 class="font-medium text-red-500 mb-4">Delete Account</h4>
                                <p class="text-sm text-gray-600 mb-4">
                                    Once you delete your account, there is no going back. Please be certain.
                                </p>
                                <button class="text-red-500 border border-red-500 px-6 py-2 rounded-full hover:bg-red-50 transition">
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
                    <li><a href="../ContactPage/contact.html" class="text-gray-600 hover:text-primary transition">Contact</a></li>
                    <li><a href="../CartPage/cart.html" class="text-gray-600 hover:text-primary transition">Cart</a></li>
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
<div id="success-toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 flex items-center">
    <i class="fas fa-check-circle mr-2"></i>
    <span>Changes saved successfully!</span>
</div>

<script>
    // Initialize cart count
    document.addEventListener('DOMContentLoaded', function() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartCount = document.querySelector('.cart-count');
        cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);

        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

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
                    } else {
                        content.classList.add('hidden');
                    }
                });
            });
        });

        // Form submission handling
        const profileForm = document.getElementById('profile-form');
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Show success toast
            const toast = document.getElementById('success-toast');
            toast.classList.remove('translate-y-full', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-full', 'opacity-0');
            }, 3000);
        });

        // Logout functionality
        document.getElementById('logout-button').addEventListener('click', function() {
            // Clear user session data
            localStorage.removeItem('user');
            localStorage.removeItem('isLoggedIn');

            // Clear cart and wishlist data (optional - remove if you want to keep these)
            localStorage.removeItem('cart');
            localStorage.removeItem('wishlist');

            // Redirect to sign in page
            window.location.href = '../SignIn&Up/signUp.php';
        });
    });
</script>
</body>
</html> 