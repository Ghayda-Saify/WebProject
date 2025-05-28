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
$sales_sql = "SELECT SUM(total_price) AS total FROM orders WHERE status != 'Pending'";
$sales_result = $conn->query($sales_sql);
$total_sales = $sales_result->fetch_assoc()['total'] ?? 0;

// Total Orders
$order_sql = "SELECT COUNT(*) AS count FROM orders WHERE status != 'Pending'";
$order_result = $conn->query($order_sql);
$total_orders = $order_result->fetch_assoc()['count'] ?? 0;

// Total Customers
$customer_sql = "SELECT COUNT(*) AS count FROM users WHERE type = 'user'";
$customer_result = $conn->query($customer_sql);
$total_customers = $customer_result->fetch_assoc()['count'] ?? 0;

// Average Order Value
$avg_order_value = $total_orders > 0 ? $total_sales / $total_orders : 0;

// Growth Rate Calculation (Month over Month)
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

// Monthly Sales Data for Chart
$monthly_sales_data = [];
$monthly_sales_labels = [];

$monthly_sales_sql = "
SELECT DATE_FORMAT(created_at, '%M') AS month_name, SUM(total_price) AS total
FROM orders
WHERE status != 'Pending' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY created_at ASC
";
$monthly_sales_result = $conn->query($monthly_sales_sql);
while ($row = $monthly_sales_result->fetch_assoc()) {
$monthly_sales_labels[] = $row['month_name'];
$monthly_sales_data[] = $row['total'];
}

// Top Selling Products
$top_products_sql = "
SELECT p.name, SUM(oi.quantity) AS quantity
FROM order_items oi
JOIN product p ON oi.product_id = p.id
GROUP BY p.id
ORDER BY quantity DESC LIMIT 5
";
$top_products_result = $conn->query($top_products_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alandalus Design | Analytics</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css ">
    <link href="https://fonts.googleapis.com/css2?family=Poppins :wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com "></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
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
            <a href="analytics.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition active bg-white/10">
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
                <h1 class="text-2xl font-bold">Analytics Dashboard</h1>
                <p class="text-gray-600">Monitor your business performance</p>
            </div>

        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Sales -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-500 text-sm">Total Sales</h3>
                    <span class="text-primary bg-primary/10 p-2 rounded">
                        <i class="fas fa-dollar-sign"></i>
                    </span>
                </div>
                <p class="text-2xl font-bold">₪ <?= number_format($total_sales, 2) ?></p>
                <p class="text-green-500 text-sm mt-2">
                    <i class="fas fa-arrow-up"></i>
                    <span><?= number_format($growth_rate, 1) ?>% from last month</span>
                </p>
            </div>

            <!-- Total Orders -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-500 text-sm">Total Orders</h3>
                    <span class="text-secondary bg-secondary/10 p-2 rounded">
                        <i class="fas fa-shopping-bag"></i>
                    </span>
                </div>
                <p class="text-2xl font-bold"><?= $total_orders ?></p>
                <p class="text-green-500 text-sm mt-2">
                    <i class="fas fa-arrow-up"></i>
                    <span><?= number_format((($total_orders / max(1, $total_orders)) * 10), 1) ?>% from last month</span>
                </p>
            </div>

            <!-- Total Customers -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-500 text-sm">Total Customers</h3>
                    <span class="text-blue-500 bg-blue-500/10 p-2 rounded">
                        <i class="fas fa-users"></i>
                    </span>
                </div>
                <p class="text-2xl font-bold"><?= $total_customers ?></p>
                <p class="text-green-500 text-sm mt-2">
                    <i class="fas fa-arrow-up"></i>
                    <span>8% from last month</span>
                </p>
            </div>

            <!-- Avg Order Value -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-500 text-sm">Avg. Order Value</h3>
                    <span class="text-purple-500 bg-purple-500/10 p-2 rounded">
                        <i class="fas fa-chart-line"></i>
                    </span>
                </div>
                <p class="text-2xl font-bold">₪ <?= number_format($avg_order_value, 2) ?></p>
                <p class="text-red-500 text-sm mt-2">
                    <i class="fas fa-arrow-down"></i>
                    <span>3% from last month</span>
                </p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Sales Chart -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold mb-4">Sales Overview</h3>
                <canvas id="salesChart" height="100"></canvas>
            </div>

            <!-- Top Products Chart -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold mb-4">Top Products</h3>
                <canvas id="topProductsChart" height="100"></canvas>
            </div>
        </div>
    </main>
</div>

<!-- ChartJS Script -->
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?= '"'.implode('","', $monthly_sales_labels).'"' ?: '"No Data"' ?>],
    datasets: [{
        label: 'Sales (₪)',
        data: [<?= implode(',', $monthly_sales_data) ?: '0' ?>],
    borderColor: '#122c6f',
        backgroundColor: 'rgba(18, 44, 111, 0.1)',
        tension: 0.4,
        fill: true,
        pointRadius: 3
    }]
    },
    options: {
        responsive: true,
            scales: {
            y: {
                beginAtZero: true,
                    ticks: {
                    callback: function(value) { return '₪' + value; }
                }
            }
        },
        plugins: {
            legend: false
        }
    }
    });

    const ctx2 = document.getElementById('topProductsChart').getContext('2d');
    const topProductsChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: [
                <?php
    $product_names = [];
    $product_quantities = [];
    while ($row = $top_products_result->fetch_assoc()) {
        $product_names[] = "'" . addslashes($row['name']) . "'";
        $product_quantities[] = $row['quantity'];
    }
    echo implode(',', $product_names);
        ?>
    ],
    datasets: [{
        label: 'Units Sold',
        data: [<?= implode(',', $product_quantities) ?: '0' ?>],
    backgroundColor: '#f13b1c'
    }]
    },
    options: {
        responsive: true,
            indexAxis: 'y',
            scales: {
            x: {
                ticks: {
                    beginAtZero: true
                }
            }
        },
        plugins: {
            legend: false
        }
    }
    });
</script>

</body>
</html>