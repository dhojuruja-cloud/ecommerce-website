<?php
require_once __DIR__ . '/../../includes/functions.php';

if (!isset($_GET['order_id'])) {
    setFlash('error', 'Payment not found.');
    redirect('account/orders.php');
}

$orderId = (int) $_GET['order_id'];
$order = db()->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
$order->execute([$orderId]);
$order = $order->fetch();

if (!$order) {
    setFlash('error', 'Order not found.');
    redirect('account/orders.php');
}

$payment = db()->prepare('SELECT * FROM payments WHERE order_id = ? LIMIT 1');
$payment->execute([$orderId]);
$payment = $payment->fetch();

if (($payment['status'] ?? '') !== 'paid') {
    db()->prepare('UPDATE payments SET status = ?, payment_response = ? WHERE order_id = ?')->execute(['paid', json_encode(['gateway' => 'eSewa', 'status' => 'success']), $orderId]);
    db()->prepare('UPDATE orders SET payment_status = ?, order_status = ? WHERE id = ?')->execute(['paid', 'confirmed', $orderId]);
}

setFlash('success', 'Payment completed successfully.');
redirect('account/order-details.php?id=' . $orderId);
