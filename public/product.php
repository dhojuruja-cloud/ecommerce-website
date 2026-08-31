<?php
require_once __DIR__ . '/../includes/functions.php';

$productId = (int) ($_GET['id'] ?? 0);
$product = getProductById($productId);

if (!$product) {
    setFlash('error', 'Product not found.');
    redirect('products.php');
}

$pageTitle = $product['name'];
include __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row g-5 align-items-center">
        <div class="col-lg-6">
            <img src="<?= htmlspecialchars(productImage($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded-4 shadow-sm w-100" style="max-height:560px; object-fit:cover;">
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <span class="badge bg-light text-dark border"><?= htmlspecialchars($product['category_name']) ?></span>
            </div>
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <h3 class="text-primary my-3"><?= money((float)$product['price']) ?></h3>
            <p><?= htmlspecialchars($product['description']) ?></p>
            <div class="mb-4">
                <?php if ((int)$product['stock'] > 0): ?>
                    <span class="text-success fw-bold">In Stock: <?= (int)$product['stock'] ?> available</span>
                <?php else: ?>
                    <span class="text-danger fw-bold">Out of stock</span>
                <?php endif; ?>
            </div>

            <?php if ((int)$product['stock'] > 0): ?>
                <form method="post" action="cart.php" class="d-flex gap-3 align-items-center flex-wrap">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                    <input type="hidden" name="return_to" value="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'] ?? 'products.php') . (($_SERVER['QUERY_STRING'] ?? '') !== '' ? '?' . $_SERVER['QUERY_STRING'] : '')) ?>">
                    <div class="qty-box">
                        <button type="button" class="qty-minus" aria-label="Decrease quantity">-</button>
                        <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>" data-qty>
                        <button type="button" class="qty-plus" aria-label="Increase quantity">+</button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">Add to Cart</button>
                </form>
            <?php endif; ?>

            <div class="mt-5 p-4 border rounded-4 bg-white">
                <h5>Product Details</h5>
                <ul class="mb-0">
                    <li>Category: <?= htmlspecialchars($product['category_name']) ?></li>
                    <li>Availability: <?= (int)$product['stock'] > 0 ? 'Available' : 'Unavailable' ?></li>
                    <li>Condition: Brand new</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
