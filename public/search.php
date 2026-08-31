<?php
require_once __DIR__ . '/../includes/functions.php';

$q = trim((string) ($_GET['q'] ?? ''));
$where = "WHERE p.status = 'active'";
$params = [];

if ($q !== '') {
    $where .= " AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
    $term = '%' . $q . '%';
    $params = [$term, $term, $term];
}

$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id {$where} ORDER BY p.created_at DESC";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$pageTitle = 'Search';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
    <div class="container text-center">
        <h1>Search Results</h1>
    </div>
</div>

<div class="container py-5">
    <?php if ($q !== ''): ?>
        <h4 class="mb-4">Search results for: "<?= htmlspecialchars($q) ?>"</h4>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <div class="empty-state">
            <h3>No products found.</h3>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <?php include __DIR__ . '/../includes/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
