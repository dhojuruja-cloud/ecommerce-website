<?php
require_once __DIR__ . '/../../includes/functions.php';

$orderId = (int) ($_GET['order_id'] ?? 0);
$transactionId = $_GET['transaction_id'] ?? '';
if ($orderId <= 0 || $transactionId === '') {
    setFlash('error', 'Invalid payment callback.');
    redirect('account/orders.php');
}

$payment = db()->prepare('SELECT * FROM payments WHERE order_id = ? AND transaction_id = ? LIMIT 1');
$payment->execute([$orderId, $transactionId]);
$paymentData = $payment->fetch();

if (!$paymentData) {
    setFlash('error', 'Payment record not found.');
    redirect('account/orders.php');
}

if ($paymentData['status'] === 'paid') {
    setFlash('success', 'Payment already processed.');
    redirect('account/order-details.php?id=' . $orderId);
}

$config = require __DIR__ . '/../../config/payment.php';
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $config['khalti']['verification_url']);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Authorization: Key ' . $config['khalti']['secret_key'],
    'Content-Type: application/json',
]);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(['token' => $_GET['token'] ?? '', 'amount' => (int) round((float) $paymentData['amount'] * 100)]));
$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response, true);
if (!empty($result['status']) && $result['status'] === 'Completed') {
    db()->prepare('UPDATE payments SET status = ?, payment_response = ?, updated_at = NOW() WHERE id = ?')->execute(['paid', json_encode($result), $paymentData['id']]);
    db()->prepare('UPDATE orders SET payment_status = ?, order_status = ?, updated_at = NOW() WHERE id = ?')->execute(['paid', 'confirmed', $orderId]);
    setFlash('success', 'Payment completed successfully.');
    redirect('account/order-details.php?id=' . $orderId);
}

db()->prepare('UPDATE payments SET status = ?, payment_response = ?, updated_at = NOW() WHERE id = ?')->execute(['failed', json_encode($result), $paymentData['id']]);
db()->prepare('UPDATE orders SET payment_status = ?, order_status = ?, updated_at = NOW() WHERE id = ?')->execute(['failed', 'pending', $orderId]);
setFlash('error', 'Payment verification failed. Please contact support.');
redirect('account/order-details.php?id=' . $orderId);
