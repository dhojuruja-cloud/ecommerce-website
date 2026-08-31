<?php
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/index.php' : 'index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('login.php');
    }

    $errors = [];
    if ($email === '' || $password === '') {
        $errors[] = 'Email and password are required.';
    }

    $user = getUserByEmail($email);
    if (!$user || !password_verify($password, $user['password'])) {
        $errors[] = 'Invalid email or password.';
    }

    if ($user && $user['status'] !== 'active') {
        $errors[] = 'Your account is currently inactive.';
    }

    if (!$errors) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        $redirectTarget = $_SESSION['redirect_after_login'] ?? ($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
        unset($_SESSION['redirect_after_login']);

        if (!is_string($redirectTarget) || $redirectTarget === '') {
            $redirectTarget = $user['role'] === 'admin' ? 'admin/index.php' : 'index.php';
        }

        $redirectTarget = ltrim((string) $redirectTarget, '/');
        $redirectTarget = strpos($redirectTarget, 'http') === 0 ? parse_url($redirectTarget, PHP_URL_PATH) ?: ($user['role'] === 'admin' ? 'admin/index.php' : 'index.php') : $redirectTarget;
        $redirectTarget = $redirectTarget === '' ? ($user['role'] === 'admin' ? 'admin/index.php' : 'index.php') : $redirectTarget;

        setFlash('success', 'Welcome back!');
        redirect($redirectTarget);
    }
}

$pageTitle = 'Login';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
    <div class="container text-center">
        <h1>Login</h1>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="form-card">
                <form method="post" action="login.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password"><i class="fa-solid fa-eye"></i></button>
                        </div>
                    </div>

                    <?php if (!empty($errors ?? [])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                        <a href="register.php?redirect_to=<?= urlencode($_SESSION['redirect_after_login'] ?? 'index.php') ?>" class="btn btn-outline-dark">Create an account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
