<?php
require_once __DIR__ . '/../../../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_user') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('admin/users/index.php');
    }

    $userId = (int) ($_POST['user_id'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $role = $_POST['role'] ?? 'customer';

    if ($userId === 1) {
        $stmt = db()->prepare('SELECT role FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $currentRole = $stmt->fetchColumn();
        if ($currentRole === 'admin') {
            setFlash('error', 'The primary administrator account cannot be modified.');
            redirect('admin/users/index.php');
        }
    }

    $stmt = db()->prepare('UPDATE users SET status = ?, role = ? WHERE id = ?');
    $stmt->execute([$status, $role, $userId]);
    setFlash('success', 'User updated successfully.');
    redirect('admin/users/index.php');
}

$stmt = db()->query('SELECT * FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();

$pageTitle = 'Manage Users';
include __DIR__ . '/../../../includes/header.php';
?>

<div class="container-fluid px-0">
    <div class="row g-0">
        <aside class="col-lg-2 admin-sidebar">
            <h4 class="mb-4">MarketNest</h4>
            <nav class="nav flex-column gap-2">
                <a class="nav-link" href="../index.php">Dashboard</a>
                <a class="nav-link" href="../products/index.php">Products</a>
                <a class="nav-link" href="../categories/index.php">Categories</a>
                <a class="nav-link" href="../orders/index.php">Orders</a>
                <a class="nav-link active" href="index.php">Users</a>
                <a class="nav-link" href="../payments/index.php">Payments</a>
                <a class="nav-link" href="<?= APP_URL ?>/logout.php">Logout</a>
            </nav>
        </aside>

        <main class="col-lg-10 bg-light min-vh-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Users</h2>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Registration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['name']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['role']) ?></td>
                                        <td><?= htmlspecialchars($user['status']) ?></td>
                                        <td><?= htmlspecialchars(date('d M Y', strtotime($user['created_at']))) ?></td>
                                        <td>
                                            <?php if ((int)$user['id'] !== 1): ?>
                                                <form method="post" action="index.php" class="d-flex gap-2">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                                    <input type="hidden" name="action" value="update_user">
                                                    <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                                    <select class="form-select form-select-sm" name="status">
                                                        <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                    <select class="form-select form-select-sm" name="role">
                                                        <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                </form>
                                            <?php endif; ?>
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
