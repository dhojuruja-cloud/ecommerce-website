<?php
require_once __DIR__ . '/../../../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('admin/orders/index.php');
    }

    $orderId = (int) ($_POST['order_id'] ?? 0);
    $status = $_POST['order_status'] ?? 'pending';
    $paymentStatus = $_POST['payment_status'] ?? 'pending';

    $stmt = db()->prepare('UPDATE orders SET order_status = ?, payment_status = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$status, $paymentStatus, $orderId]);
    setFlash('success', 'Order status updated.');
    redirect('admin/orders/index.php');
}

$stmt = db()->query('SELECT o.*, u.name AS customer_name FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC');
$orders = $stmt->fetchAll();

$pageTitle = 'Manage Orders';
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
                <a class="nav-link active" href="index.php">Orders</a>
                <a class="nav-link" href="../users/index.php">Users</a>
                <a class="nav-link" href="../payments/index.php">Payments</a>
                <a class="nav-link" href="<?= APP_URL ?>/logout.php">Logout</a>
            </nav>
        </aside>

        <main class="col-lg-10 bg-light min-vh-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Orders</h2>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Order Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td><?= money((float)$order['total_amount']) ?></td>
                                        <td><?= renderPaymentBadge($order['payment_status']) ?></td>
                                        <td><?= renderOrderBadge($order['order_status']) ?></td>
                                        <td>
                                            <form method="post" action="index.php" class="d-flex flex-wrap gap-2">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                                <select class="form-select form-select-sm" name="order_status">
                                                    <?php foreach (['pending','confirmed','processing','shipped','delivered','cancelled'] as $status): ?>
                                                        <option value="<?= $status ?>" <?= $order['order_status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <select class="form-select form-select-sm" name="payment_status">
                                                    <?php foreach (['pending','paid','failed','refunded'] as $status): ?>
                                                        <option value="<?= $status ?>" <?= $order['payment_status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                            </form>
                                        </td>
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
