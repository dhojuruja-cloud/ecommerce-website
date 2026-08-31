<?php
require_once __DIR__ . '/functions.php';
$flash = getFlash();
$cartCount = getCartCount();
$user = currentUser();
$siteName = APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Modern e-commerce storefront for demo products and secure shopping.">
    <title><?= htmlspecialchars($siteName) ?> | <?= htmlspecialchars($pageTitle ?? 'Shop') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>

<?php if ($flash): ?>
<div class="container mt-3">
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>
