<?php
$user = currentUser();
$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold brand-text" href="<?= APP_URL ?>/index.php">
            <i class="fa-solid fa-bag-shopping me-2"></i><?= htmlspecialchars(APP_NAME) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'products.php' ? 'active' : '' ?>" href="<?= APP_URL ?>/products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'categories.php' ? 'active' : '' ?>" href="<?= APP_URL ?>/categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'search.php' ? 'active' : '' ?>" href="<?= APP_URL ?>/search.php">Search</a></li>
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?= APP_URL ?>/cart.php">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Cart
                        <?php if ($cartCount > 0): ?>
                            <span class="cart-badge"><?= (int) $cartCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>

            <form class="d-flex me-3 search-form" action="<?= APP_URL ?>/search.php" method="get">
                <input class="form-control" type="search" name="q" placeholder="Search products..." aria-label="Search products">
            </form>

            <?php if ($user): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($user['name']) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if (($user['role'] ?? 'customer') === 'admin'): ?>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/index.php">Admin Dashboard</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/account/index.php">My Account</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/account/orders.php">My Orders</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= APP_URL ?>/logout.php">Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="<?= APP_URL ?>/login.php" class="btn btn-outline-dark me-2">Login</a>
                <a href="<?= APP_URL ?>/register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
