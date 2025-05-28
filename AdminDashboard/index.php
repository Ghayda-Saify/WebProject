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

// Total Sales
$total_sales_sql = "SELECT SUM(total_price) AS total FROM orders WHERE status != 'Pending'";
$total_sales_result = $conn->query($total_sales_sql);
$total_sales = $total_sales_result->fetch_assoc()['total'] ?? 0;

// Total Customers
$total_customers_sql = "SELECT COUNT(*) AS count FROM users WHERE type = 'user'";
$total_customers_result = $conn->query($total_customers_sql);
$total_customers = $total_customers_result->fetch_assoc()['count'] ?? 0;

// Total Products
$total_products_sql = "SELECT COUNT(*) AS count FROM product";
$total_products_result = $conn->query($total_products_sql);
$total_products = $total_products_result->fetch_assoc()['count'] ?? 0;

// Growth Rate Calculation
$growth_sql = "
    SELECT 
        SUM(CASE WHEN YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE()) THEN total_price ELSE 0 END) AS current_month,
        SUM(CASE WHEN YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE()) - 1 THEN total_price ELSE 0 END) AS previous_month
    FROM orders WHERE status != 'Pending'
";
$growth_result = $conn->query($growth_sql);
$growth_row = $growth_result->fetch_assoc();

$current_month = max(1, floatval($growth_row['current_month'])); // Avoid division by zero
$previous_month = floatval($growth_row['previous_month']);
$growth_rate = $previous_month == 0 ? 0 : (($current_month - $previous_month) / $previous_month) * 100;

// Recent Orders with Real Customer Names
$recent_orders_sql = "
    SELECT 
        o.order_id,
        u.name AS customer,
        p.name AS product,
        o.total_price,
        o.status
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN product p ON oi.product_id = p.id
    ORDER BY o.created_at DESC LIMIT 5
";
$recent_orders_result = $conn->query($recent_orders_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Alandalus Design | Admin Dashboard</title>
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
            <a href="index.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition active bg-white/10">
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
    <main class="ml-64 flex-1 p-8">
        <!-- Top Bar -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold">Dashboard Overview</h1>
                <p class="text-gray-600">Welcome back, Admin</p>
            </div>
            <div class="flex items-center space-x-4">

                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=122c6f&color=fff "
                         alt="Admin"
                         class="w-10 h-10 rounded-full cursor-pointer">
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-primary">
                        <i class="fas fa-shopping-bag text-2xl"></i>
                    </div>
                    <span class="text-green-500 flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <?= number_format($growth_rate, 1) ?>%
                    </span>
                </div>
                <h3 class="text-gray-600 text-sm">Total Sales</h3>
                <p class="text-2xl font-semibold">₪ <?= number_format($total_sales, 2) ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-secondary">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <span class="text-green-500 flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <?= number_format((($total_customers / max(1, $total_customers)) * 10), 1) ?>%
                    </span>
                </div>
                <h3 class="text-gray-600 text-sm">Total Customers</h3>
                <p class="text-2xl font-semibold"><?= $total_customers ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-yellow-500">
                        <i class="fas fa-box text-2xl"></i>
                    </div>
                    <span class="text-red-500 flex items-center">
                        <i class="fas fa-arrow-down mr-1"></i>
                        3%
                    </span>
                </div>
                <h3 class="text-gray-600 text-sm">Total Products</h3>
                <p class="text-2xl font-semibold"><?= $total_products ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-green-500">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <span class="text-green-500 flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <?= number_format($growth_rate, 1) ?>%
                    </span>
                </div>
                <h3 class="text-gray-600 text-sm">Growth Rate</h3>
                <p class="text-2xl font-semibold"><?= number_format($growth_rate, 1) ?>%</p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">Recent Orders</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <?php if ($recent_orders_result && $recent_orders_result->num_rows > 0): ?>
                        <?php while ($row = $recent_orders_result->fetch_assoc()): ?>
                            <?php
                            $status = strtolower($row['status']);
                            switch ($status):
                                case 'completed': case 'delivered':
                                $badge = 'bg-green-100 text-green-800';
                                break;
                                case 'in production':
                                    $badge = 'bg-blue-100 text-blue-800';
                                    break;
                                default:
                                    $badge = 'bg-yellow-100 text-yellow-800';
                            endswitch;
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">#ORD-<?= htmlspecialchars($row['order_id']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['customer']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['product']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">₪ <?= number_format($row['total_price'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $badge ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">No recent orders found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                <div class="space-y-4">
                    <a href="add_product.php" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition block">
                        <span class="flex items-center">
                            <i class="fas fa-plus-circle text-primary mr-3"></i>
                            Add New Product
                        </span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    <a href="promotions.php" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition block">
                        <span class="flex items-center">
                            <i class="fas fa-tag text-secondary mr-3"></i>
                            Create Discount
                        </span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Recent Activity</h2>
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <p class="text-sm">New customer registration</p>
                            <p class="text-xs text-gray-500">Just now</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-500">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <p class="text-sm">New order received</p>
                            <p class="text-xs text-gray-500">5 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-500">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <p class="text-sm">New product review</p>
                            <p class="text-xs text-gray-500">1 hour ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>