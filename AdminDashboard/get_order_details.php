<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "alandalus";

$con =  mysqli_connect("127.0.0.1", "root", "", "alandalus");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$order_id = intval($_GET['order_id']);

$sql = "
    SELECT 
        o.*,
        u.email,
        GROUP_CONCAT(CONCAT(p.name, ' x ', oi.quantity) SEPARATOR '<br>') AS items
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN product p ON oi.product_id = p.id
    WHERE o.order_id = ?
    GROUP BY o.order_id
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()):
    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="font-semibold text-lg">Order Info</h3>
            <p><strong>Order ID:</strong> #<?= $row['order_id'] ?></p>
            <p><strong>Date:</strong> <?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
        </div>
        <div>
            <h3 class="font-semibold text-lg">Customer Info</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?></p>
        </div>
        <div class="col-span-2">
            <h3 class="font-semibold text-lg">Shipping Address</h3>
            <p><?= nl2br(htmlspecialchars($row['address'])) ?></p>
            <p><?= htmlspecialchars($row['city'] . ', ' . $row['state'] . ', ZIP: ' . $row['zip']) ?></p>
        </div>
        <div class="col-span-2">
            <h3 class="font-semibold text-lg">Items</h3>
            <p><?= $row['items'] ?></p>
        </div>
        <div class="col-span-2">
            <h3 class="font-semibold text-lg">Total</h3>
            <p class="text-xl font-bold">₪<?= number_format($row['total_price'], 2) ?></p>
        </div>
    </div>
<?php else: ?>
    <p class="text-red-500">Order not found.</p>
<?php endif; ?>