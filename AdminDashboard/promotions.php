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

// Handle promotion creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = $conn->real_escape_string($_POST['title']);
    $subtitle = $conn->real_escape_string($_POST['subtitle']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $btn_text = $conn->real_escape_string($_POST['btn_text']);
    $btn_link = $conn->real_escape_string($_POST['btn_link']);
    $color = $conn->real_escape_string($_POST['color']);
    $discount_value = floatval($_POST['discount_value']);
    $discount_type = $conn->real_escape_string($_POST['discount_type']);

    $sql = "
        INSERT INTO promotions (
            title, subtitle, image_url, btn_text, btn_link, color, active, discount_value, discount_type
        ) VALUES (
            ?, ?, ?, ?, ?, ?, 1, ?, ?
        )
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssdid", $title, $subtitle, $image_url, $btn_text, $btn_link, $color, $discount_value, $discount_type);
    $stmt->execute();
    $stmt->close();
    header("Location: promotions.php");
    exit;
}

// Fetch all promotions
$sql = "SELECT * FROM promotions ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alandalus Design | Promotions Management</title>
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
            <a href="settings.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 flex-1 p-8 overflow-auto">
        <!-- Top Bar -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold">Promotions Management</h1>
                <p class="text-gray-600">Create, edit, or delete promotions</p>
            </div>
        </div>

        <!-- Add Promotion Form -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Add New Promotion</h2>
            <form method="post">
                <input type="hidden" name="action" value="create">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" id="title" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label for="subtitle" class="block text-gray-700 text-sm font-medium mb-1">Subtitle</label>
                    <input type="text" name="subtitle" id="subtitle" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="image_url" class="block text-gray-700 text-sm font-medium mb-1">Image URL</label>
                    <input type="text" name="image_url" id="image_url" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="btn_text" class="block text-gray-700 text-sm font-medium mb-1">Button Text</label>
                    <input type="text" name="btn_text" id="btn_text" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="btn_link" class="block text-gray-700 text-sm font-medium mb-1">Button Link</label>
                    <input type="text" name="btn_link" id="btn_link" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="color" class="block text-gray-700 text-sm font-medium mb-1">Color</label>
                    <input type="text" name="color" id="color" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="discount_value" class="block text-gray-700 text-sm font-medium mb-1">Discount Value</label>
                    <input type="number" step="0.01" name="discount_value" id="discount_value" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="discount_type" class="block text-gray-700 text-sm font-medium mb-1">Discount Type</label>
                    <select name="discount_type" id="discount_type" class="w-full border rounded px-3 py-2">
                        <option value="percent">Percent</option>
                        <option value="amount">Amount</option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">Create Promotion</button>
            </form>
        </div>

        <!-- Existing Promotions Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtitle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image URL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['id']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['title']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['subtitle']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Promotion Image" class="w-16 h-16 object-cover"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="edit_promotion.php?id=<?= htmlspecialchars($row['id']) ?>" class="text-primary hover:underline">Edit</a>
                                <form action="delete_promotion.php" method="post" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <button type="submit" class="text-red-500 hover:underline ml-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>