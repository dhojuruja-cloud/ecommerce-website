<?php
require_once __DIR__ . '/../../../includes/functions.php';
requireAdmin();

$productId = (int) ($_GET['id'] ?? 0);
$product = getProductById($productId);
if (!$product) {
    setFlash('error', 'Product not found.');
    redirect('admin/products/index.php');
}

$categories = db()->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('admin/products/index.php');
    }

    $name = sanitize($_POST['name'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $status = $_POST['status'] ?? $product['status'];

    $errors = [];
    if ($name === '' || $categoryId <= 0 || $price <= 0 || $stock < 0) {
        $errors[] = 'Please provide valid product details.';
    }

    if (empty($errors)) {
        $imageName = $product['image'];
        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true) || $file['size'] > MAX_UPLOAD_SIZE) {
                $errors[] = 'Invalid image file. Please upload JPG, PNG, or WEBP up to 2MB.';
            } else {
                $imageName = 'product_' . uniqid() . '.' . $ext;
                move_uploaded_file($file['tmp_name'], __DIR__ . '/../../uploads/' . $imageName);
            }
        }
    }

    if (empty($errors)) {
        $stmt = db()->prepare('UPDATE products SET category_id = ?, name = ?, slug = ?, description = ?, price = ?, stock = ?, image = ?, status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$categoryId, $name, slugify($name), $description, $price, $stock, $imageName, $status, $productId]);
        setFlash('success', 'Product updated successfully.');
        redirect('admin/products/index.php');
    }
}

$pageTitle = 'Edit Product';
include __DIR__ . '/../../../includes/header.php';
?>

<div class="container-fluid px-0">
    <div class="row g-0">
        <aside class="col-lg-2 admin-sidebar">
            <h4 class="mb-4">MarketNest</h4>
            <nav class="nav flex-column gap-2">
                <a class="nav-link" href="../index.php">Dashboard</a>
                <a class="nav-link active" href="index.php">Products</a>
                <a class="nav-link" href="../categories/index.php">Categories</a>
                <a class="nav-link" href="../orders/index.php">Orders</a>
                <a class="nav-link" href="../users/index.php">Users</a>
                <a class="nav-link" href="../payments/index.php">Payments</a>
                <a class="nav-link" href="<?= APP_URL ?>/logout.php">Logout</a>
            </nav>
        </aside>

        <main class="col-lg-10 bg-light min-vh-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Edit Product</h2>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <?php if (!empty($errors ?? [])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? $product['name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= (int)$category['id'] ?>" <?= ((int)($_POST['category_id'] ?? $product['category_id']) === (int)$category['id']) ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? $product['description']) ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" name="price" required value="<?= htmlspecialchars($_POST['price'] ?? $product['price']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stock</label>
                                <input type="number" class="form-control" name="stock" required value="<?= htmlspecialchars($_POST['stock'] ?? $product['stock']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?= (($product['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                                    <option value="draft" <?= (($product['status'] ?? 'active') === 'draft') ? 'selected' : '' ?>>Draft</option>
                                    <option value="inactive" <?= (($product['status'] ?? 'active') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Image</label>
                                <input type="file" class="form-control" name="image" accept="image/png,image/jpeg,image/webp">
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Product</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
