<?php
require_once __DIR__ . '/../includes/functions.php';

$categoryFilter = (int) ($_GET['category'] ?? 0);
$searchTerm = trim((string) ($_GET['q'] ?? ''));
$sort = $_GET['sort'] ?? 'featured';
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

$categories = db()->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC")->fetchAll();

$where = ["p.status = 'active'"];
$params = [];

if ($categoryFilter > 0) {
    $where[] = 'p.category_id = ?';
    $params[] = $categoryFilter;
}

if ($searchTerm !== '') {
    $where[] = '(p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)';
    $term = '%' . $searchTerm . '%';
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE " . implode(' AND ', $where);

switch ($sort) {
    case 'price_asc':
        $sql .= ' ORDER BY p.price ASC';
        break;
    case 'price_desc':
        $sql .= ' ORDER BY p.price DESC';
        break;
    case 'new':
        $sql .= ' ORDER BY p.created_at DESC';
        break;
    default:
        $sql .= ' ORDER BY p.name ASC';
        break;
}

$countStmt = db()->prepare('SELECT COUNT(*) AS total FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE ' . implode(' AND ', $where));
$countStmt->execute($params);
$totalProducts = (int) $countStmt->fetch()['total'];

$sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
$totalPages = (int) ceil($totalProducts / $limit);

$pageTitle = 'Products';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
    <div class="container text-center">
        <h1>Products</h1>
    </div>
</div>

<div class="container py-5">
    <div class="filter-bar">
        <form method="get" action="products.php" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="<?= htmlspecialchars($searchTerm) ?>" class="form-control" placeholder="Search products...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int)$category['id'] ?>" <?= $categoryFilter === (int)$category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sort by</label>
                <select name="sort" class="form-select">
                    <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Featured</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>Newest</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Apply</button>
            </div>
        </form>
    </div>

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

        <?php if ($totalPages > 1): ?>
            <nav class="mt-5" aria-label="Pagination">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                            <a class="page-link" href="products.php?category=<?= (int)$categoryFilter ?>&q=<?= urlencode($searchTerm) ?>&sort=<?= urlencode($sort) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
