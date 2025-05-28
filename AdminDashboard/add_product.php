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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = intval($_POST['category_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $mrp = floatval($_POST['mrp']);
    $price = floatval($_POST['price']);
    $qty = intval($_POST['qty']);
    $image = $conn->real_escape_string($_POST['image']);
    $short_desc = $conn->real_escape_string($_POST['short_desc']);
    $description = $conn->real_escape_string($_POST['description']);
    $meta_title = $conn->real_escape_string($_POST['meta_title']);
    $meta_desc = $conn->real_escape_string($_POST['meta_desc']);
    $meta_keyword = $conn->real_escape_string($_POST['meta_keyword']);
    $status = intval($_POST['status']);

    $stmt = $conn->prepare("
        INSERT INTO product (
            category_id, name, mrp, price, qty, image, short_desc, description, meta_title, meta_desc, meta_keyword, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die('Prepare() failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param(
        "isddissssssi",
        $category_id, $name, $mrp, $price, $qty, $image, $short_desc, $description, $meta_title, $meta_desc, $meta_keyword, $status
    );

    if ($stmt->execute()) {
        header("Location: products.php");
        exit;
    } else {
        echo "<div class='bg-red-100 text-red-800 p-4 rounded mb-6'>Error adding product: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Fetch categories for dropdown
$category_sql = "SELECT * FROM categories WHERE status = 1";
$category_result = $conn->query($category_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product | Alandalus Design</title>
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
                        secondary: "#f13b1c"
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
            <a href="products.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition active bg-white/10">
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
            <a href="settings.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 flex-1 p-8">
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
            <h2 class="text-2xl font-semibold mb-6">Add New Product</h2>
            <form method="post" class="space-y-6">
                <!-- Category + Product Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Category</label>
                        <select name="category_id" required class="w-full border rounded px-3 py-2">
                            <option value="">Select Category</option>
                            <?php while ($cat_row = $category_result->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($cat_row['id']) ?>">
                                    <?= htmlspecialchars($cat_row['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Product Name</label>
                        <input type="text" name="name" required class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <!-- MRP + Selling Price -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">MRP (Market Retail Price)</label>
                        <input type="number" step="0.01" name="mrp" required class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Selling Price</label>
                        <input type="number" step="0.01" name="price" required class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <!-- Quantity + Image URL -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Quantity</label>
                        <input type="number" name="qty" required class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Image URL</label>
                        <input type="text" name="image" required class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <!-- Short Description -->
                <div>
                    <label class="block text-sm font-medium mb-1">Short Description</label>
                    <textarea name="short_desc" rows="2" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <!-- Full Description -->
                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <!-- Meta Tags -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta Title</label>
                        <input type="text" name="meta_title" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta Description</label>
                        <input type="text" name="meta_desc" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta Keywords</label>
                        <input type="text" name="meta_keyword" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded hover:bg-opacity-90 transition">
                        Add Product
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>