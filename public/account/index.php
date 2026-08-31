<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$user = currentUser();
if (!$user) {
    redirect('login.php');
}

$statsStmt = db()->prepare('SELECT COUNT(*) AS total_orders, COALESCE(SUM(total_amount), 0) AS total_spent FROM orders WHERE user_id = ?');
$statsStmt->execute([(int)$user['id']]);
$stats = $statsStmt->fetch();

$recentOrdersStmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$recentOrdersStmt->execute([(int)$user['id']]);
$recentOrders = $recentOrdersStmt->fetchAll();

$pageTitle = 'My Account';
include __DIR__ . '/../../includes/header.php';
?>

<div class="page-banner">
    <div class="container text-center">
        <h1>My Account</h1>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="summary-card">
                <h4>Profile Details</h4>
                <div class="mt-3">
                    <div class="mb-3">
                        <small class="text-muted d-block">Full Name</small>
                        <strong><?= htmlspecialchars($user['name']) ?></strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <strong><?= htmlspecialchars($user['email']) ?></strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Phone</small>
                        <strong><?= htmlspecialchars($user['phone'] ?: 'Not provided') ?></strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">City</small>
                        <strong><?= htmlspecialchars($user['city'] ?: 'Not provided') ?></strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Address</small>
                        <strong><?= htmlspecialchars($user['address'] ?: 'Not provided') ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="summary-card">
                <h4>Account Summary</h4>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted">Total Orders</small>
                            <h4 class="mb-0 mt-2"><?= (int)($stats['total_orders'] ?? 0) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted">Total Spent</small>
                            <h4 class="mb-0 mt-2"><?= money((float)($stats['total_spent'] ?? 0)) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted">Account Status</small>
                            <h4 class="mb-0 mt-2 text-success"><?= htmlspecialchars(ucfirst($user['status'] ?? 'active')) ?></h4>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="orders.php" class="btn btn-sm btn-outline-primary">View all</a>
                    </div>

                    <?php if (empty($recentOrders)): ?>
                        <div class="empty-state small">
                            <h6>You haven't placed any orders yet.</h6>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle bg-white mb-0">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($order['order_number']) ?></td>
                                            <td><?= htmlspecialchars(date('d M Y', strtotime($order['created_at']))) ?></td>
                                            <td><?= money((float)$order['total_amount']) ?></td>
                                            <td><?= renderOrderBadge($order['order_status']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
