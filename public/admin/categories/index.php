<?php
require_once __DIR__ . '/../../../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('admin/categories/index.php');
    }

    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $stmt = db()->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
    $stmt->execute([$categoryId]);
    if ((int) $stmt->fetchColumn() > 0) {
        setFlash('error', 'This category is in use by products and cannot be deleted.');
        redirect('admin/categories/index.php');
    }

    $stmt = db()->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$categoryId]);
    setFlash('success', 'Category deleted successfully.');
    redirect('admin/categories/index.php');
}

$stmt = db()->query('SELECT * FROM categories ORDER BY created_at DESC');
$categories = $stmt->fetchAll();

$pageTitle = 'Manage Categories';
include __DIR__ . '/../../../includes/header.php';
?>

<div class="container-fluid px-0">
    <div class="row g-0">
        <aside class="col-lg-2 admin-sidebar">
            <h4 class="mb-4">MarketNest</h4>
            <nav class="nav flex-column gap-2">
                <a class="nav-link" href="../index.php">Dashboard</a>
                <a class="nav-link" href="../products/index.php">Products</a>
                <a class="nav-link active" href="index.php">Categories</a>
                <a class="nav-link" href="../orders/index.php">Orders</a>
                <a class="nav-link" href="../users/index.php">Users</a>
                <a class="nav-link" href="../payments/index.php">Payments</a>
                <a class="nav-link" href="<?= APP_URL ?>/logout.php">Logout</a>
            </nav>
        </aside>

        <main class="col-lg-10 bg-light min-vh-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Categories</h2>
                <a href="create.php" class="btn btn-primary">Add Category</a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($category['name']) ?></td>
                                        <td><?= htmlspecialchars($category['description']) ?></td>
                                        <td><?= htmlspecialchars($category['status']) ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="edit.php?id=<?= (int)$category['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form method="post" action="index.php">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="category_id" value="<?= (int)$category['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger confirm-delete">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
