<?php
require_once __DIR__ . '/../includes/functions.php';

$stmt = db()->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC");
$categories = $stmt->fetchAll();

$pageTitle = 'Categories';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
    <div class="container text-center">
        <h1>Categories</h1>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <?php foreach ($categories as $category): ?>
            <div class="col-md-6 col-lg-4">
                <div class="category-card h-100">
                    <div class="category-image">
                        <img src="<?= htmlspecialchars(productImage($category['image'])) ?>" alt="<?= htmlspecialchars($category['name']) ?>">
                    </div>
                    <div class="category-body">
                        <h4><?= htmlspecialchars($category['name']) ?></h4>
                        <p><?= htmlspecialchars($category['description']) ?></p>
                        <a href="products.php?category=<?= (int)$category['id'] ?>" class="btn btn-primary">View Products</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
