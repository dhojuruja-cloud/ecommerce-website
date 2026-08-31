<?php
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('register.php');
    }

    $errors = [];

    if ($name === '' || $email === '' || $phone === '' || $address === '' || $city === '' || $password === '' || $confirmPassword === '') {
        $errors[] = 'Please fill in all required fields.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (getUserByEmail($email)) {
        $errors[] = 'This email is already registered.';
    }

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO users (name, email, password, phone, address, city, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT), $phone, $address, $city, 'customer', 'active']);

        $redirectTo = trim((string) ($_GET['redirect_to'] ?? $_SESSION['redirect_after_login'] ?? 'index.php'));
        if ($redirectTo !== '') {
            $_SESSION['redirect_after_login'] = $redirectTo;
        }

        setFlash('success', 'Registration successful. Please log in.');
        redirect('login.php');
    }
}

$pageTitle = 'Register';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-banner">
    <div class="container text-center">
        <h1>Register</h1>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="form-card">
                <form method="post" action="register.php" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" required value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password"><i class="fa-solid fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password"><i class="fa-solid fa-eye"></i></button>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($errors ?? [])): ?>
                        <div class="alert alert-danger mt-4">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
