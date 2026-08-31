<?php
require_once __DIR__ . '/../includes/functions.php';

$stmt = db()->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC LIMIT 6");
$categories = $stmt->fetchAll();

$stmt = db()->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.status = 'active' ORDER BY created_at DESC LIMIT 8");
$latestProducts = $stmt->fetchAll();

$stmt = db()->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.status = 'active' ORDER BY price DESC LIMIT 4");
$featuredProducts = $stmt->fetchAll();

$pageTitle = 'Home';
include __DIR__ . '/../includes/header.php';
?>

<main>
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <span class="hero-badge">Fresh picks for modern living</span>
                    <h1>Shop smarter with MarketNest.</h1>
                    <p>Discover everyday essentials, tech gadgets, home upgrades, and style favorites curated for your life.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="products.php" class="btn btn-primary btn-lg">Shop Now</a>
                        <a href="categories.php" class="btn btn-outline-dark btn-lg">Browse Categories</a>
                    </div>
                    <div class="hero-stats mt-4 d-flex gap-4 flex-wrap">
                        <div><strong>20K+</strong><span>Happy shoppers</span></div>
                        <div><strong>5K+</strong><span>Products delivered</span></div>
                        <div><strong>4.9/5</strong><span>Customer rating</span></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-card">
                        <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=900&q=80" alt="Shopping lifestyle" class="img-fluid rounded-4">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="section-title d-flex justify-content-between align-items-center mb-4">
            <h2>Popular Categories</h2>
            <a href="categories.php" class="btn btn-link text-dark">View all</a>
        </div>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="category-card">
                        <div class="category-image">
                            <img src="<?= htmlspecialchars(productImage($category['image'])) ?>" alt="<?= htmlspecialchars($category['name']) ?>">
                        </div>
                        <div class="category-body">
                            <h5><?= htmlspecialchars($category['name']) ?></h5>
                            <p><?= htmlspecialchars($category['description']) ?></p>
                            <a href="products.php?category=<?= (int)$category['id'] ?>" class="btn btn-sm btn-outline-primary">Explore</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="container py-5">
        <div class="section-title d-flex justify-content-between align-items-center mb-4">
            <h2>Featured Products</h2>
            <a href="products.php" class="btn btn-link text-dark">View all</a>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col-sm-6 col-lg-3">
                    <?php include __DIR__ . '/../includes/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="container py-5">
        <div class="section-title d-flex justify-content-between align-items-center mb-4">
            <h2>New Arrivals</h2>
            <a href="products.php?sort=new" class="btn btn-link text-dark">See latest</a>
        </div>
        <div class="row g-4">
            <?php foreach ($latestProducts as $product): ?>
                <div class="col-sm-6 col-lg-3">
                    <?php include __DIR__ . '/../includes/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="cta-section">
        <div class="container text-center">
            <h2>Upgrade your everyday essentials.</h2>
            <p>Enjoy secure checkout, fast fulfillment, and quality picks you’ll love.</p>
            <a href="products.php" class="btn btn-primary btn-lg">Start Shopping</a>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
