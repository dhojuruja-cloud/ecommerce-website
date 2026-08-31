<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

function sanitize(string $value): string
{
    return trim(strip_tags($value));
}

function redirect(string $path): void
{
    $target = strpos($path, 'http') === 0 ? $path : APP_URL . '/' . ltrim($path, '/');
    header('Location: ' . $target);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to continue.');
        redirect('login.php');
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        setFlash('error', 'Access denied.');
        redirect('login.php');
    }
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]+/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function money(float $value): string
{
    return 'Rs. ' . number_format($value, 2);
}

function formatDate(?string $date): string
{
    if (!$date) {
        return 'N/A';
    }

    return date('d M Y, h:i A', strtotime($date));
}

function getCart(): array
{
    return $_SESSION['cart'] ?? [];
}

function addToCart(int $productId, int $quantity = 1): void
{
    $cart = getCart();
    $productId = (int) $productId;
    $quantity = max(1, (int) $quantity);

    if (isset($cart[$productId])) {
        $cart[$productId] += $quantity;
    } else {
        $cart[$productId] = $quantity;
    }

    $_SESSION['cart'] = $cart;
}

function updateCartQuantity(int $productId, int $quantity): void
{
    $cart = getCart();
    $quantity = max(0, (int) $quantity);

    if ($quantity <= 0) {
        unset($cart[$productId]);
    } else {
        $cart[$productId] = $quantity;
    }

    $_SESSION['cart'] = $cart;
}

function removeFromCart(int $productId): void
{
    $cart = getCart();
    unset($cart[$productId]);
    $_SESSION['cart'] = $cart;
}

function clearCart(): void
{
    unset($_SESSION['cart']);
}

function getCartCount(): int
{
    $total = 0;
    foreach (getCart() as $qty) {
        $total += (int) $qty;
    }
    return $total;
}

function generateOrderNumber(): string
{
    return 'ORD-' . date('Y') . '-' . str_pad((string) rand(1000, 9999), 5, '0', STR_PAD_LEFT);
}

function getProductById(int $id): ?array
{
    $stmt = db()->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ? LIMIT 1');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function getUserByEmail(string $email): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    return $stmt->fetch() ?: null;
}

function getCartSummary(array $cart): array
{
    if (empty($cart)) {
        return ['items' => [], 'subtotal' => 0, 'shipping' => 0, 'total' => 0, 'count' => 0];
    }

    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT * FROM products WHERE id IN ({$placeholders}) AND status = 'active'");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    $items = [];
    $subtotal = 0;
    $count = 0;

    foreach ($cart as $productId => $quantity) {
        $product = null;
        foreach ($products as $row) {
            if ((int) $row['id'] === (int) $productId) {
                $product = $row;
                break;
            }
        }

        if (!$product) {
            continue;
        }

        $lineTotal = (float) $product['price'] * (int) $quantity;
        $subtotal += $lineTotal;
        $count += (int) $quantity;

        $items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'image' => $product['image'],
            'price' => (float) $product['price'],
            'quantity' => (int) $quantity,
            'stock' => (int) $product['stock'],
            'subtotal' => $lineTotal,
        ];
    }

    $shipping = $subtotal >= SHIPPING_THRESHOLD ? 0 : SHIPPING_FEE;
    $total = $subtotal + $shipping;

    return ['items' => $items, 'subtotal' => $subtotal, 'shipping' => $shipping, 'total' => $total, 'count' => $count];
}

function renderProductBadge(string $status): string
{
    $class = 'bg-secondary';
    if ($status === 'active') {
        $class = 'bg-success';
    }
    if ($status === 'draft') {
        $class = 'bg-warning text-dark';
    }
    if ($status === 'inactive') {
        $class = 'bg-secondary';
    }
    return '<span class="badge ' . $class . '">' . htmlspecialchars(ucwords($status)) . '</span>';
}

function renderOrderBadge(string $status): string
{
    $class = 'bg-secondary';
    $map = [
        'pending' => 'bg-warning text-dark',
        'confirmed' => 'bg-primary',
        'processing' => 'bg-info',
        'shipped' => 'bg-dark',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger',
    ];
    $class = $map[$status] ?? $class;
    return '<span class="badge ' . $class . '">' . htmlspecialchars(ucwords($status)) . '</span>';
}

function renderPaymentBadge(string $status): string
{
    $class = 'bg-secondary';
    $map = [
        'paid' => 'bg-success',
        'pending' => 'bg-warning text-dark',
        'failed' => 'bg-danger',
        'refunded' => 'bg-info',
    ];
    $class = $map[$status] ?? $class;
    return '<span class="badge ' . $class . '">' . htmlspecialchars(ucwords($status)) . '</span>';
}

function productImage(?string $image): string
{
    if ($image) {
        if (preg_match('/^https?:\/\//i', $image)) {
            return $image;
        }

        $localFile = __DIR__ . '/../public/uploads/' . basename($image);
        if (file_exists($localFile)) {
            return APP_URL . '/uploads/' . basename($image);
        }
    }

    return 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=900&q=80';
}
