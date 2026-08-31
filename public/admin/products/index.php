<?php
require_once __DIR__ . '/../../../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('admin/products/index.php');
    }

    $productId = (int) ($_POST['product_id'] ?? 0);
    $stmt = db()->prepare('SELECT COUNT(*) FROM order_items WHERE product_id = ?');
    $stmt->execute([$productId]);
    $orderCount = (int) $stmt->fetchColumn();

    if ($orderCount > 0) {
        setFlash('error', 'This product is already referenced by existing orders and cannot be deleted.');
        redirect('admin/products/index.php');
    }

    $stmt = db()->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    setFlash('success', 'Product deleted successfully.');
    redirect('admin/products/index.php');
}

$stmt = db()->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC');
$products = $stmt->fetchAll();

$pageTitle = 'Manage Products';
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
                <h2>Products</h2>
                <a href="create.php" class="btn btn-primary">Add Product</a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><img src="<?= htmlspecialchars(productImage($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;"></td>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                                        <td><?= money((float)$product['price']) ?></td>
                                        <td><?= (int)$product['stock'] ?></td>
                                        <td><?= $product['status'] ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="edit.php?id=<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form method="post" action="index.php">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
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
