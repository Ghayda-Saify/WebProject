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

$product_id = intval($_GET['id'] ?? 0);

// Fetch product details
$sql = "SELECT * FROM product WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

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

    $update_sql = "
        UPDATE product SET 
            category_id = ?, name = ?, mrp = ?, price = ?, qty = ?, image = ?, 
            short_desc = ?, description = ?, meta_title = ?, meta_desc = ?, 
            meta_keyword = ?, status = ?
        WHERE id = ?";

    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param(
        "issddissssssi",
        $category_id, $name, $mrp, $price, $qty, $image,
        $short_desc, $description, $meta_title, $meta_desc,
        $meta_keyword, $status, $product_id
    );

    if ($stmt_update->execute()) {
        header("Location: products.php");
        exit;
    } else {
        echo "<div class='bg-red-100 text-red-800 p-4 rounded mb-6'>Error updating product: " . $stmt_update->error . "</div>";
    }

    $stmt_update->close();
}

// Fetch categories for dropdown
$category_sql = "SELECT id, name FROM categories WHERE status = 1";
$category_result = $conn->query($category_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | Alandalus Design</title>
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
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 flex-1 p-8">
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
            <h2 class="text-2xl font-semibold mb-6">Edit Product</h2>
            <form method="post" class="space-y-6">
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium mb-1">Category</label>
                    <select name="category_id" required class="w-full border rounded px-3 py-2">
                        <?php
                        $category_result->data_seek(0); // Reset pointer
                        while ($cat_row = $category_result->fetch_assoc()):
                            $selected = $cat_row['id'] == $product['category_id'] ? 'selected' : '';
                            ?>
                            <option value="<?= $cat_row['id'] ?>" <?= $selected ?>>
                                <?= htmlspecialchars($cat_row['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Product Name -->
                <div>
                    <label class="block text-sm font-medium mb-1">Product Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="w-full border rounded px-3 py-2">
                </div>

                <!-- MRP & Price -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">MRP (Market Retail Price)</label>
                        <input type="number" step="0.01" name="mrp" value="<?= $product['mrp'] ?>" required class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Selling Price</label>
                        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <!-- Quantity & Image -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Quantity</label>
                        <input type="number" name="qty" value="<?= $product['qty'] ?>" required class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Image URL</label>
                        <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>" required class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <!-- Short Description -->
                <div>
                    <label class="block text-sm font-medium mb-1">Short Description</label>
                    <textarea name="short_desc" rows="2" class="w-full border rounded px-3 py-2"><?= htmlspecialchars($product['short_desc']) ?></textarea>
                </div>

                <!-- Full Description -->
                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full border rounded px-3 py-2"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <!-- Meta Tags -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta Title</label>
                        <input type="text" name="meta_title" value="<?= htmlspecialchars($product['meta_title']) ?>" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta Description</label>
                        <input type="text" name="meta_desc" value="<?= htmlspecialchars($product['meta_desc']) ?>" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta Keywords</label>
                        <input type="text" name="meta_keyword" value="<?= htmlspecialchars($product['meta_keyword']) ?>" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2">
                        <option value="1" <?= $product['status'] == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $product['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="products.php" class="px-6 py-2 border rounded hover:bg-gray-100 transition">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded hover:bg-opacity-90 transition">Update Product</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>