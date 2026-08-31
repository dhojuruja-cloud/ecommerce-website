<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$orderId = (int) ($_GET['id'] ?? 0);
$user = currentUser();

$stmt = db()->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$orderId, $user['id']]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Order not found.');
    redirect('account/orders.php');
}

$itemsStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();

$pageTitle = 'Order Details';
include __DIR__ . '/../../includes/header.php';
?>

<div class="page-banner">
    <div class="container text-center">
        <h1>Order Details</h1>
    </div>
</div>

<div class="container py-5">
    <div class="summary-card mb-4">
        <div class="d-flex justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="mb-1"><?= htmlspecialchars($order['order_number']) ?></h4>
                <small class="text-muted">Placed on <?= htmlspecialchars(date('d M Y', strtotime($order['created_at']))) ?></small>
            </div>
            <div>
                <?= renderOrderBadge($order['order_status']) ?>
                <?= renderPaymentBadge($order['payment_status']) ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="summary-card">
                <h5>Customer Information</h5>
                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['shipping_name']) ?></p>
                <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($order['shipping_phone']) ?></p>
                <p class="mb-0"><strong>Address:</strong> <?= htmlspecialchars($order['shipping_address']) ?>, <?= htmlspecialchars($order['shipping_city']) ?></p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="summary-card">
                <h5>Order Summary</h5>
                <p class="mb-1"><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                <p class="mb-1"><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
                <p class="mb-0"><strong>Order Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
            </div>
        </div>
    </div>

    <div class="summary-card mt-4">
        <h5>Products</h5>
        <div class="table-responsive mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= (int)$item['quantity'] ?></td>
                            <td><?= money((float)$item['price']) ?></td>
                            <td><?= money((float)$item['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <div class="d-flex justify-content-between"><span>Subtotal</span><strong><?= money((float)$order['subtotal']) ?></strong></div>
            <div class="d-flex justify-content-between"><span>Shipping</span><strong><?= (float)$order['shipping_fee'] > 0 ? money((float)$order['shipping_fee']) : 'Free' ?></strong></div>
            <div class="d-flex justify-content-between mt-2"><span>Total</span><strong><?= money((float)$order['total_amount']) ?></strong></div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
