<?php
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$totalProducts = (int) db()->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalCategories = (int) db()->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$totalUsers = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalOrders = (int) db()->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$pendingOrders = (int) db()->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'")->fetchColumn();
$completedOrders = (int) db()->query("SELECT COUNT(*) FROM orders WHERE order_status = 'delivered'")->fetchColumn();
$totalSales = (float) db()->query('SELECT COALESCE(SUM(total_amount), 0) FROM orders')->fetchColumn();
$recentOrders = db()->query('SELECT o.*, u.name AS customer_name FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT 5')->fetchAll();

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../../includes/header.php';
?>

<div class="container-fluid px-0">
    <div class="row g-0">
        <aside class="col-lg-2 admin-sidebar">
            <h4 class="mb-4">MarketNest</h4>
            <nav class="nav flex-column gap-2">
                <a class="nav-link active" href="index.php">Dashboard</a>
                <a class="nav-link" href="products/index.php">Products</a>
                <a class="nav-link" href="categories/index.php">Categories</a>
                <a class="nav-link" href="orders/index.php">Orders</a>
                <a class="nav-link" href="users/index.php">Users</a>
                <a class="nav-link" href="payments/index.php">Payments</a>
                <a class="nav-link" href="<?= APP_URL ?>/logout.php">Logout</a>
            </nav>
        </aside>

        <main class="col-lg-10 bg-light min-vh-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Dashboard</h2>
                <span class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6 col-xl-4"><div class="stats-card"><div class="text-muted">Total Products</div><h4><?= $totalProducts ?></h4></div></div>
                <div class="col-md-6 col-xl-4"><div class="stats-card"><div class="text-muted">Total Categories</div><h4><?= $totalCategories ?></h4></div></div>
                <div class="col-md-6 col-xl-4"><div class="stats-card"><div class="text-muted">Total Users</div><h4><?= $totalUsers ?></h4></div></div>
                <div class="col-md-6 col-xl-4"><div class="stats-card"><div class="text-muted">Total Orders</div><h4><?= $totalOrders ?></h4></div></div>
                <div class="col-md-6 col-xl-4"><div class="stats-card"><div class="text-muted">Pending Orders</div><h4><?= $pendingOrders ?></h4></div></div>
                <div class="col-md-6 col-xl-4"><div class="stats-card"><div class="text-muted">Completed Orders</div><h4><?= $completedOrders ?></h4></div></div>
                <div class="col-md-6 col-xl-4"><div class="stats-card"><div class="text-muted">Total Sales</div><h4><?= money((float)$totalSales) ?></h4></div></div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Recent Orders</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td><?= money((float)$order['total_amount']) ?></td>
                                        <td><?= renderOrderBadge($order['order_status']) ?></td>
                                        <td><?= renderPaymentBadge($order['payment_status']) ?></td>
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

<?php include __DIR__ . '/../../includes/footer.php'; ?>
