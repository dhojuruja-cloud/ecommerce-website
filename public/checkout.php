<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$user = currentUser();
$cartSummary = getCartSummary(getCart());
$items = $cartSummary['items'];

if (empty($items)) {
    setFlash('error', 'Your cart is empty.');
    redirect('products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('checkout.php');
    }

    $shippingName = sanitize($_POST['shipping_name'] ?? '');
    $shippingPhone = sanitize($_POST['shipping_phone'] ?? '');
    $shippingAddress = sanitize($_POST['shipping_address'] ?? '');
    $shippingCity = sanitize($_POST['shipping_city'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'Cash on Delivery';

    $errors = [];
    if ($shippingName === '' || $shippingPhone === '' || $shippingAddress === '' || $shippingCity === '') {
        $errors[] = 'Please fill in all shipping details.';
    }

    if (!in_array($paymentMethod, ['Khalti', 'eSewa', 'Cash on Delivery'], true)) {
        $errors[] = 'Invalid payment method.';
    }

    $validatedCart = [];
    $productIds = [];
    foreach ($items as $item) {
        $product = getProductById((int)$item['id']);
        if (!$product) {
            $errors[] = 'One or more products are unavailable.';
            continue;
        }
        if ((int)$item['quantity'] > (int)$product['stock']) {
            $errors[] = 'Insufficient stock for ' . $product['name'] . '.';
        }
        $validatedCart[] = $product;
        $productIds[] = $product['id'];
    }

    if (!$errors) {
        $pdo = db();
        $orderNumber = generateOrderNumber();
        $subtotal = (float) $cartSummary['subtotal'];
        $shippingFee = (float) $cartSummary['shipping'];
        $totalAmount = (float) $cartSummary['total'];

        try {
            $pdo->beginTransaction();

            $orderStmt = $pdo->prepare('INSERT INTO orders (user_id, order_number, subtotal, shipping_fee, total_amount, payment_method, payment_status, order_status, shipping_name, shipping_phone, shipping_address, shipping_city) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $orderStmt->execute([
                (int) $user['id'],
                $orderNumber,
                $subtotal,
                $shippingFee,
                $totalAmount,
                $paymentMethod,
                'pending',
                'pending',
                $shippingName,
                $shippingPhone,
                $shippingAddress,
                $shippingCity,
            ]);
            $orderId = $pdo->lastInsertId();

            foreach ($items as $item) {
                $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?, ?)')->execute([
                    $orderId,
                    (int) $item['id'],
                    $item['name'],
                    (int) $item['quantity'],
                    (float) $item['price'],
                    (float) $item['subtotal'],
                ]);

                $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?')->execute([
                    (int) $item['quantity'],
                    (int) $item['id'],
                    (int) $item['quantity'],
                ]);
            }

            $paymentStmt = $pdo->prepare('INSERT INTO payments (order_id, user_id, payment_method, transaction_id, amount, status, payment_response) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $paymentStmt->execute([
                $orderId,
                (int) $user['id'],
                $paymentMethod,
                null,
                $totalAmount,
                'pending',
                json_encode(['status' => 'pending'])
            ]);

            $pdo->commit();
            clearCart();

            if ($paymentMethod === 'Khalti') {
                $_SESSION['pending_order_id'] = $orderId;
                redirect('payment/khalti-initiate.php?order_id=' . $orderId);
            }

            if ($paymentMethod === 'eSewa') {
                $_SESSION['pending_order_id'] = $orderId;
                redirect('payment/esewa-success.php?order_id=' . $orderId . '&status=success');
            }

            $pdo->prepare('UPDATE orders SET payment_status = ?, order_status = ? WHERE id = ?')->execute(['paid', 'confirmed', $orderId]);
            $pdo->prepare('UPDATE payments SET status = ?, payment_response = ? WHERE order_id = ?')->execute(['paid', json_encode(['status' => 'cash_on_delivery']), $orderId]);
            setFlash('success', 'Order placed successfully.');
            redirect('account/orders.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            setFlash('error', 'Unable to place order. Please try again.');
            redirect('checkout.php');
        }
    }
}

$pageTitle = 'Checkout';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
    <div class="container text-center">
        <h1>Checkout</h1>
    </div>
</div>

<div class="container py-5">
    <?php if (!empty($errors ?? [])): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="checkout.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="summary-card mb-4">
                    <h4>Customer Information</h4>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['phone'] ?: '') ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="summary-card">
                    <h4>Shipping Information</h4>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="shipping_name" required value="<?= htmlspecialchars($_POST['shipping_name'] ?? $user['name']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="shipping_phone" required value="<?= htmlspecialchars($_POST['shipping_phone'] ?? $user['phone']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="shipping_address" rows="3" required><?= htmlspecialchars($_POST['shipping_address'] ?? $user['address']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="shipping_city" required value="<?= htmlspecialchars($_POST['shipping_city'] ?? $user['city']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <h4>Order Summary</h4>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between mt-3">
                            <div>
                                <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                                <small><?= (int)$item['quantity'] ?> × <?= money((float)$item['price']) ?></small>
                            </div>
                            <span><?= money((float)$item['subtotal']) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Subtotal</span><strong><?= money((float)$cartSummary['subtotal']) ?></strong></div>
                    <div class="d-flex justify-content-between mt-2"><span>Shipping</span><strong><?= $cartSummary['shipping'] > 0 ? money((float)$cartSummary['shipping']) : 'Free' ?></strong></div>
                    <div class="d-flex justify-content-between mt-2"><span>Total</span><strong><?= money((float)$cartSummary['total']) ?></strong></div>

                    <div class="mt-4">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method">
                            <option value="Khalti">Khalti</option>
                            <option value="eSewa">eSewa</option>
                            <option value="Cash on Delivery">Cash on Delivery</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-4">Place Order</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
