<?php
session_start();

// Database connection
$host = "127.0.0.1";
$user = "root"; // Change if needed
$password = ""; // Update accordingly
$dbname = "alandalus";

$con = new mysqli($host, $user, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $con->real_escape_string($_POST['new_status']);

    $stmt = $con->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: orders.php");
    exit;
}

// Fetch all orders with customer and product info
$sql = "
    SELECT 
        o.order_id,
        o.first_name,
        o.last_name,
        GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names,
        o.total_price,
        o.created_at,
        o.status
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN product p ON oi.product_id = p.id
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
";
$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Alandalus Design | Orders Management</title>
    <link rel="stylesheet" href="admin.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css " />
    <link href="https://fonts.googleapis.com/css2?family=Poppins :wght@300;400;500;600;700&display=swap" rel="stylesheet" />
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
            <a href="orders.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition active bg-white/10">
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
                <h1 class="text-2xl font-bold">Orders Management</h1>
                <p class="text-gray-600">Track and manage customer orders</p>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Update Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $status = strtolower($row['status']);
                        switch ($status):
                            case 'pending': $badge = 'bg-yellow-100 text-yellow-800'; break;
                            case 'in production': $badge = 'bg-blue-100 text-blue-800'; break;
                            case 'completed': $badge = 'bg-green-100 text-green-800'; break;
                            case 'delivered': $badge = 'bg-green-100 text-green-800'; break;
                            default: $badge = 'bg-gray-100 text-gray-800';
                        endswitch;
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">#<?= htmlspecialchars($row['order_id']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['product_names']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">₪<?= number_format($row['total_price'], 2) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $badge ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <form method="post" class="inline">
                                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                    <select name="new_status" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                                        <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="In Production" <?= $row['status'] == 'In Production' ? 'selected' : '' ?>>In Production</option>
                                        <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="Delivered" <?= $row['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button class="text-primary hover:underline" onclick="showOrderDetails(<?= $row['order_id'] ?>)">View</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-4xl w-full mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Order Details</h2>
            <button class="text-gray-400 hover:text-gray-600" onclick="closeOrderModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="orderDetails" class="space-y-6">
            <!-- AJAX will load order details here -->
        </div>
        <div class="mt-8 flex justify-end space-x-4">
            <button onclick="closeOrderModal()" class="px-6 py-2 border rounded-lg hover:bg-gray-100 transition">Close</button>
        </div>
    </div>
</div>

<script>
    function showOrderDetails(orderId) {
        fetch(`get_order_details.php?order_id=${orderId}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('orderDetails').innerHTML = data;
                document.getElementById('orderModal').classList.remove('hidden');
                document.getElementById('orderModal').classList.add('flex');
            })
            .catch(err => {
                console.error(err);
                document.getElementById('orderDetails').innerHTML = "<p>Error loading order details.</p>";
                document.getElementById('orderModal').classList.remove('hidden');
                document.getElementById('orderModal').classList.add('flex');
            });
    }

    function closeOrderModal() {
        document.getElementById('orderModal').classList.add('hidden');
        document.getElementById('orderModal').classList.remove('flex');
    }
</script>
</body>
</html>