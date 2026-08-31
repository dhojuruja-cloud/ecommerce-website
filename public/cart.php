<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isLoggedIn()) {
    $returnTo = trim((string) ($_POST['return_to'] ?? 'products.php'));
    if ($returnTo === '') {
        $returnTo = 'products.php';
    }
    $_SESSION['redirect_after_login'] = $returnTo;
    setFlash('error', 'Please login or register to continue shopping.');
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('cart.php');
    }

    $action = $_POST['action'] ?? '';
    $productId = (int) ($_POST['product_id'] ?? 0);

    if ($action === 'add') {
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $product = getProductById($productId);
        if (!$product) {
            setFlash('error', 'Product not found.');
            redirect('products.php');
        }

        $cart = getCart();
        $existing = $cart[$productId] ?? 0;
        if (($existing + $quantity) > (int)$product['stock']) {
            setFlash('error', 'Only ' . (int)$product['stock'] . ' items are available in stock.');
        } else {
            addToCart($productId, $quantity);
            setFlash('success', 'Product added to cart.');
        }
        redirect('cart.php');
    }

    if ($action === 'update') {
        $quantity = max(0, (int) ($_POST['quantity'] ?? 0));
        $product = getProductById($productId);
        if ($product && $quantity > (int)$product['stock']) {
            setFlash('error', 'Only ' . (int)$product['stock'] . ' items are available in stock.');
        } else {
            updateCartQuantity($productId, $quantity);
            setFlash('success', 'Cart updated.');
        }
        redirect('cart.php');
    }

    if ($action === 'remove') {
        removeFromCart($productId);
        setFlash('success', 'Product removed from cart.');
        redirect('cart.php');
    }

    if ($action === 'clear') {
        clearCart();
        setFlash('success', 'Cart cleared.');
        redirect('cart.php');
    }
}

$cartSummary = getCartSummary(getCart());
$items = $cartSummary['items'];
$pageTitle = 'Cart';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
    <div class="container text-center">
        <h1>Shopping Cart</h1>
    </div>
</div>

<div class="container py-5">
    <?php if (empty($items)): ?>
        <div class="empty-state">
            <h3>Your cart is empty.</h3>
            <a href="products.php" class="btn btn-primary mt-3">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table cart-table table-bordered align-middle bg-white">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= htmlspecialchars(productImage($item['image'])) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-image">
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($item['name']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= money((float)$item['price']) ?></td>
                                    <td>
                                        <form method="post" action="cart.php" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                                            <div class="qty-box">
                                                <button type="button" class="qty-minus">-</button>
                                                <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= (int)$item['stock'] ?>" data-qty>
                                                <button type="button" class="qty-plus">+</button>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                        </form>
                                    </td>
                                    <td><?= money((float)$item['subtotal']) ?></td>
                                    <td>
                                        <form method="post" action="cart.php">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <form method="post" action="cart.php" class="mt-3">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn btn-outline-secondary">Clear Cart</button>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <h4>Order Summary</h4>
                    <div class="d-flex justify-content-between mt-3"><span>Subtotal</span><strong><?= money((float)$cartSummary['subtotal']) ?></strong></div>
                    <div class="d-flex justify-content-between mt-2"><span>Shipping</span><strong><?= $cartSummary['shipping'] > 0 ? money((float)$cartSummary['shipping']) : 'Free' ?></strong></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Total</span><strong><?= money((float)$cartSummary['total']) ?></strong></div>
                    <a href="checkout.php" class="btn btn-primary w-100 mt-4">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
