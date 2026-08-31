<div class="product-card h-100">
    <div class="product-image-wrap">
        <img src="<?= htmlspecialchars(productImage($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid">
    </div>
    <div class="p-3 d-flex flex-column h-100">
        <div class="small text-muted mb-2"><?= htmlspecialchars($product['category_name'] ?? 'General') ?></div>
        <h5 class="product-title"><?= htmlspecialchars($product['name']) ?></h5>
        <div class="pricing mt-auto mb-3">
            <span class="price"><?= money((float)$product['price']) ?></span>
        </div>
        <div class="product-meta mb-3">
            <?php if ((int)$product['stock'] > 0): ?>
                <span class="text-success">In Stock</span>
            <?php else: ?>
                <span class="text-danger">Out of Stock</span>
            <?php endif; ?>
        </div>
        <div class="d-flex flex-column gap-2 mt-auto product-actions">
            <a href="product.php?id=<?= (int)$product['id'] ?>" class="btn btn-primary btn-sm fw-bold w-100">View Details</a>
            <?php if ((int)$product['stock'] > 0): ?>
                <form action="cart.php" method="post" class="d-flex align-items-center gap-2 w-100 product-cart-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                    <input type="hidden" name="return_to" value="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'] ?? 'products.php') . (($_SERVER['QUERY_STRING'] ?? '') !== '' ? '?' . $_SERVER['QUERY_STRING'] : '')) ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>" class="form-control form-control-sm product-qty" style="width:60px;" data-qty>
                    <button type="submit" class="btn btn-success flex-grow-1">Add to Cart</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
