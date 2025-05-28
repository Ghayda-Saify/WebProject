<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "alandalus";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$customer_id = intval($_GET['customer_id']);

// Get customer info
$customer_sql = "SELECT * FROM users WHERE id = ? AND type = 'user'";
$stmt = $conn->prepare($customer_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer_result = $stmt->get_result();

if ($customer_result->num_rows === 0) {
    echo "<p class='text-red-500'>Customer not found.</p>";
    exit;
}

$customer = $customer_result->fetch_assoc();

// Get order history
$order_sql = "
    SELECT o.order_id, o.total_price, o.status, o.created_at
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$order_result = $stmt->get_result();
?>
    <h3><?= htmlspecialchars($customer['name']) ?></h3>
    <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone_number']) ?></p>
    <p><strong>Joined:</strong> <?= date('Y-m-d', strtotime($customer['created_at'])) ?></p>

    <h4 class="mt-6 font-semibold">Order History</h4>
    <table class="min-w-full mt-2">
        <thead>
        <tr class="bg-gray-100">
            <th class="px-4 py-2">Order ID</th>
            <th class="px-4 py-2">Amount</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Date</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($order_result->num_rows > 0): ?>
            <?php while ($order = $order_result->fetch_assoc()):
                $status = strtolower($order['status']);
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
                <tr class="border-b">
                    <td class="px-4 py-2">#<?= htmlspecialchars($order['order_id']) ?></td>
                    <td class="px-4 py-2">₪<?= number_format($order['total_price'], 2) ?></td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $badge ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-2"><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="px-4 py-2 text-center">No orders found</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
<?php $stmt->close(); ?>