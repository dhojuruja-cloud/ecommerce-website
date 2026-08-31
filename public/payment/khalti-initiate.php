<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$orderId = (int) ($_GET['order_id'] ?? 0);
if ($orderId <= 0) {
    setFlash('error', 'Invalid order selection.');
    redirect('account/orders.php');
}

$order = db()->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
$order->execute([$orderId, $_SESSION['user_id']]);
$order = $order->fetch();
if (!$order) {
    setFlash('error', 'Order not found.');
    redirect('account/orders.php');
}

$config = require __DIR__ . '/../../config/payment.php';
$amount = (int) round((float) $order['total_amount'] * 100);
$transactionId = 'KHALTI-' . uniqid();

$stmt = db()->prepare('UPDATE payments SET transaction_id = ?, status = ? WHERE order_id = ?');
$stmt->execute([$transactionId, 'pending', $orderId]);

$payload = [
    'return_url' => APP_URL . '/public/payment/khalti-callback.php?order_id=' . $orderId . '&transaction_id=' . urlencode($transactionId),
    'website_url' => APP_URL,
    'amount' => $amount,
    'purchase_order_id' => (string) $order['id'],
    'purchase_order_name' => 'MarketNest Order ' . $order['order_number'],
    'customer_info' => [
        'name' => $order['shipping_name'],
        'phone' => $order['shipping_phone'],
    ],
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $config['khalti']['payment_url']);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Authorization: Key ' . $config['khalti']['secret_key'],
    'Content-Type: application/json',
]);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response, true);
if (!empty($result['payment_url'])) {
    header('Location: ' . $result['payment_url']);
    exit;
}

setFlash('error', 'We could not initiate the payment. Please try again.');
redirect('account/orders.php');
