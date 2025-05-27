<?php
global $con;
session_start();
include '../connection.php';
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

$cart_count = 0;
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $cart_count_result = $con->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
    if ($cart_count_result && $row = $cart_count_result->fetch_assoc()) {
        $cart_count = $row['total'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Alandalus Design</title>
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
                    <a href="../ProfilePage/profile.php">
                        <i class="fa-solid fa-user text-primary"></i>
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
            <a href="../CartPage/cart.php" class="hover:text-blue-600">Cart</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800">Checkout</span>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Checkout Form -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>
                    <form id="shipping-form" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">First Name</label>
                                <input type="text" name="first_name" required class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Last Name</label>
                                <input type="text" name="last_name" required class="w-full border rounded px-3 py-2">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" name="email" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Phone</label>
                            <input type="tel" name="phone" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Address</label>
                            <input type="text" name="address" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">City</label>
                                <input type="text" name="city" required class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">State</label>
                                <input type="text" name="state" required class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">ZIP Code</label>
                                <input type="text" name="zip" required class="w-full border rounded px-3 py-2">
                            </div>
                        </div>
                    </form>

                </div>

                <!-- Payment Information Section (Commented) -->
                <!--
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Payment Information</h2>
                    <form id="payment-form" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Card Number</label>
                            <input type="text" required placeholder="1234 5678 9012 3456" class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Expiry Date</label>
                                <input type="text" required placeholder="MM/YY" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">CVV</label>
                                <input type="text" required placeholder="123" class="w-full border rounded px-3 py-2">
                            </div>
                        </div>
                    </form>
                </div>
                -->
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
                            <span>₪ <?php echo number_format($shipping, 2); ?></span>
                        </div>
                        <div class="border-t pt-3 mt-3">
                            <div class="flex justify-between font-semibold">
                                <span>Total</span>
                                <span>₪<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>
                    <button id="place-order-button" class="w-full bg-primary text-white py-3 rounded-lg mt-6 hover:bg-opacity-90 transition-colors">
                        Place Order
                    </button>
                </div>

                <!-- Promo Code -->
                <div class="bg-white rounded-lg shadow p-6 mt-4">
                    <h3 class="text-lg font-semibold mb-4">Promo Code</h3>
                    <div class="flex space-x-2">
                        <input type="text" placeholder="Enter code" class="flex-1 border rounded px-3 py-2">
                        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/search.js"></script>
    <script src="checkout.js"></script>
    <script>
        document.getElementById('place-order-button').addEventListener('click', function (e) {
            e.preventDefault();

            const form = document.getElementById('shipping-form');
            const formData = new FormData(form);

            fetch('place_order.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Order placed successfully!');
                        window.location.href = 'thank_you.php?cart_id=4';
                    } else {
                        alert('Error placing order: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while placing the order.');
                });
        });
    </script>




</body>
</html> 