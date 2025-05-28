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

// Fetch all customers (type = 'user')
$sql = "
SELECT
u.id,
u.name,
u.email,
u.phone_number,
COUNT(o.order_id) AS total_orders,
COALESCE(SUM(o.total_price), 0) AS total_spent,
u.created_at
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
WHERE u.type = 'user'
GROUP BY u.id
ORDER BY u.created_at DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alandalus Design | Customers Management</title>
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
            <a href="customers.php" class="flex items-center space-x-2 p-2 hover:bg-white/10 rounded transition active bg-white/10">
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
                <h1 class="text-2xl font-bold">Customers Management</h1>
                <p class="text-gray-600">Manage and track customer information</p>
            </div>
            <div class="flex space-x-4">

            </div>
        </div>

 <!-- Customer Filters -->
<!--        <div class="bg-white p-4 rounded-lg shadow-sm mb-6">-->
<!--            <div class="flex flex-wrap gap-4">-->
<!--                <div class="flex-1 min-w-[200px]">-->
<!--                    <input type="text" id="searchCustomer" placeholder="Search customers..."-->
<!--                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">-->
<!--                </div>-->
<!--                <select id="orderFilter" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">-->
<!--                    <option value="">All Orders</option>-->
<!--                    <option value="hasOrders">Has Orders</option>-->
<!--                    <option value="noOrders">No Orders</option>-->
<!--                </select>-->
<!--                <select id="dateFilter" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30">-->
<!--                    <option value="">All Time</option>-->
<!--                    <option value="today">Joined Today</option>-->
<!--                    <option value="week">This Week</option>-->
<!--                    <option value="month">This Month</option>-->
<!--                    <option value="year">This Year</option>-->
<!--                </select>-->
<!--            </div>-->
<!--        </div>-->

        <!-- Customers Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="customersTableBody">
                    <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= intval($row['total_orders']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">₪<?= number_format($row['total_spent'], 2) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button class="text-primary hover:underline" onclick="viewCustomer(<?= $row['id'] ?>)">View</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">No customers found.</td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- View Customer Modal -->
<div id="customerDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-4xl w-full mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Customer Details</h2>
            <button onclick="closeCustomerDetails()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="customerInfo" class="space-y-4">
            <!-- Dynamic content will be injected here -->
        </div>
        <div class="mt-8">
            <h3 class="text-xl font-semibold mb-4">Order History</h3>
            <div id="customerOrders" class="space-y-4">
                <!-- Order history will be injected here -->
            </div>
        </div>
        <div class="mt-8 flex justify-end space-x-4">
            <button onclick="closeCustomerDetails()" class="px-6 py-2 border rounded-lg hover:bg-gray-100 transition">Close</button>
        </div>
    </div>
</div>

<script>
    function viewCustomer(customerId) {
        fetch(`get_customer_details.php?customer_id=${customerId}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('customerInfo').innerHTML = data;
                document.getElementById('customerDetailsModal').classList.remove('hidden');
                document.getElementById('customerDetailsModal').classList.add('flex');
            })
            .catch(err => {
                console.error(err);
                document.getElementById('customerInfo').innerHTML = "<p>Error loading customer details.</p>";
                document.getElementById('customerDetailsModal').classList.remove('hidden');
                document.getElementById('customerDetailsModal').classList.add('flex');
            });
    }

    function closeCustomerDetails() {
        document.getElementById('customerDetailsModal').classList.add('hidden');
        document.getElementById('customerDetailsModal').classList.remove('flex');
    }
</script>
</body>
</html>