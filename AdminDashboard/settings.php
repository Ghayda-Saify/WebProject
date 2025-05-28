<?php
session_start();

// Database connection
$host = "127.0.0.1";
$user = "root"; // Update if needed
$password = ""; // Update accordingly
$dbname = "alandalus";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (isset($_POST['store_name'], $_POST['email'], $_POST['phone'], $_POST['address'])) {
// Store Info Update
$store_name = $conn->real_escape_string($_POST['store_name']);
$email = $conn->real_escape_string($_POST['email']);
$phone = $conn->real_escape_string($_POST['phone']);
$address = $conn->real_escape_string($_POST['address']);

// You can store this in a separate table or use constants/configs
// For demo, we'll just show success message
echo "<script>alert('Store info updated successfully!');</script>";
}

if (isset($_POST['currency'])) {
// Payment Settings
$currency = $conn->real_escape_string($_POST['currency']);
$payment_methods = isset($_POST['payment_methods']) ? $_POST['payment_methods'] : [];
echo "<script>alert('Payment settings saved. Currency: $currency');</script>";
}

if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
// Password change logic
$current_pass = $conn->real_escape_string($_POST['current_password']);
$new_pass = $conn->real_escape_string($_POST['new_password']);
$confirm_pass = $conn->real_escape_string($_POST['confirm_password']);

if ($new_pass !== $confirm_pass) {
echo "<script>alert('New passwords do not match!');</script>";
} else {
// Simulating admin password check - replace with actual DB check
$stored_hash = "7c4a8d09ca3762af61e59520943dc26494f8941b"; // e.g., SHA1 of 'password'

if (sha1($current_pass) === $stored_hash) {
// Update password (in real case, update hashed password in DB)
echo "<script>alert('Password changed successfully!');</script>";
} else {
echo "<script>alert('Current password is incorrect!');</script>";
}
}
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alandalus Design | Settings</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css ">
    <link href="https://fonts.googleapis.com/css2?family=Poppins :wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com "></script>
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
<body class="bg-gray-100">
<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="bg-primary w-64 h-screen fixed left-0 top-0 text-white p-4">
        <div class="mb-8">
            <h2 class="text-2xl font-['Pacifico']">Alandalus Design</h2>
            <p class="text-sm text-white/70">Admin Dashboard</p>
        </div>
        <nav class="space-y-2">
            <a href="index.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="products.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
            <a href="orders.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </a>
            <a href="customers.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
            <a href="analytics.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
            </a>
            <a href="settings.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition active bg-white/10">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="logout.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition mt-auto text-red-300 hover:text-red-100">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 flex-1 p-8">
        <!-- Top Bar -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold">Settings</h1>
            <p class="text-gray-600">Manage your store settings and preferences</p>
        </div>

        <!-- Settings Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Store Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Store Information</h2>
                <form method="post" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                        <input type="text" name="store_name" value="Alandalus Design" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="info@alandalusdesign.com" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" name="phone" value="+972 59-464-6503" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" name="address" value="Palestine, Gaza Strip" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition">
                        Save Changes
                    </button>
                </form>
            </div>

            <!-- Payment Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Payment Settings</h2>
                <form method="post" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                        <select name="currency" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">
                            <option value="ILS" selected>Israeli Shekel (₪)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Methods</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="payment_methods[]" value="Cash on Delivery" checked class="form-checkbox text-primary">
                                <span class="ml-2">Cash on Delivery</span>
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition">
                        Save Changes
                    </button>
                </form>
            </div>

            <!-- Security Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Security Settings</h2>
                <form method="post" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="new_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>


<!-- Tailwind switch toggle styling -->
<style>
    .switch-label {
        padding-left: 2.5rem;
        background-color: #cbd5e0;
    }
    .switch-label::before {
        content: '';
        position: absolute;
        width: 1.4rem;
        height: 1.4rem;
        left: 0.3rem;
        bottom: 0.3rem;
        background-color: white;
        border-radius: 9999px;
        transition: 0.4s;
    }
    input:checked + .switch-label::before {
        transform: translateX(1.4rem);
    }
</style>

<script>
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            alert('Settings saved successfully!');
        });
    });
</script>
</body>
</html>