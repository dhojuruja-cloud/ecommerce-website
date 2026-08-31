<?php
require_once __DIR__ . '/../../../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('admin/categories/index.php');
    }

    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'active';

    if ($name === '') {
        setFlash('error', 'Category name is required.');
        redirect('admin/categories/create.php');
    }

    $stmt = db()->prepare('INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, slugify($name), $description, $status]);

    setFlash('success', 'Category created successfully.');
    redirect('admin/categories/index.php');
}

$pageTitle = 'Create Category';
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
                <h2>Add Category</h2>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="4"></textarea>
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Category</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
