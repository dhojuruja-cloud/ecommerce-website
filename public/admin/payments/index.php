<?php
require_once __DIR__ . '/../../../includes/functions.php';
requireAdmin();

$stmt = db()->query('SELECT p.*, o.order_number, u.name AS customer_name FROM payments p LEFT JOIN orders o ON o.id = p.order_id LEFT JOIN users u ON u.id = p.user_id ORDER BY p.created_at DESC');
$payments = $stmt->fetchAll();

$pageTitle = 'Payment Records';
include __DIR__ . '/../../../includes/header.php';
?>

<div class="container-fluid px-0">
    <div class="row g-0">
        <aside class="col-lg-2 admin-sidebar">
            <h4 class="mb-4">MarketNest</h4>
            <nav class="nav flex-column gap-2">
                <a class="nav-link" href="../index.php">Dashboard</a>
                <a class="nav-link" href="../products/index.php">Products</a>
                <a class="nav-link" href="../categories/index.php">Categories</a>
                <a class="nav-link" href="../orders/index.php">Orders</a>
                <a class="nav-link" href="../users/index.php">Users</a>
                <a class="nav-link active" href="index.php">Payments</a>
                <a class="nav-link" href="<?= APP_URL ?>/logout.php">Logout</a>
            </nav>
        </aside>

        <main class="col-lg-10 bg-light min-vh-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Payments</h2>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Transaction</th>
                                    <th>Order</th>
                                    <th>User</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($payment['transaction_id'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($payment['order_number'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($payment['customer_name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                                        <td><?= money((float)$payment['amount']) ?></td>
                                        <td><?= renderPaymentBadge($payment['status']) ?></td>
                                        <td><?= htmlspecialchars(date('d M Y', strtotime($payment['created_at']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
