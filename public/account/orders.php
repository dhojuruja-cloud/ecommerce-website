<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$user = currentUser();
$stmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([(int)$user['id']]);
$orders = $stmt->fetchAll();

$pageTitle = 'My Orders';
include __DIR__ . '/../../includes/header.php';
?>

<div class="page-banner">
    <div class="container text-center">
        <h1>My Orders</h1>
    </div>
</div>

<div class="container py-5">
    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <h3>You haven't placed any orders yet.</h3>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered bg-white">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_number']) ?></td>
                            <td><?= htmlspecialchars(date('d M Y', strtotime($order['created_at']))) ?></td>
                            <td><?= money((float)$order['total_amount']) ?></td>
                            <td><?= renderPaymentBadge($order['payment_status']) ?></td>
                            <td><?= renderOrderBadge($order['order_status']) ?></td>
                            <td><a href="order-details.php?id=<?= (int)$order['id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
