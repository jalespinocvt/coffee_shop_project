<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/config/database.php';

define('BASE_URL', '/coffee_shop');

/* ---------- Helpers ---------- */

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/users/login.php');
        exit;
    }
}

function redirect_if_logged_in(): void
{
    if (is_logged_in()) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function format_time(?string $time): string
{
    return $time ? date('M j, Y · g:i A', strtotime($time)) : '—';
}

/* ---------- Image upload helpers (NEW) ---------- */

/* Returns [relative_path|null, error|null]. No file chosen => [null, null]. */
function upload_image(array $file, string $folder = 'uploads/orders'): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'The image could not be uploaded. Try a smaller file.'];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return [null, 'Image must be 2 MB or smaller.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
        return [null, 'Only JPG, PNG, GIF or WEBP images are allowed.'];
    }

    $dir = __DIR__ . '/' . $folder;
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        return [null, 'Upload folder could not be created.'];
    }

    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        return [null, 'Failed to save the image.'];
    }
    return [$folder . '/' . $name, null];
}

/* Removes an uploaded image file (only inside the uploads folder) */
function delete_image_file(?string $path): void
{
    if ($path && strpos($path, 'uploads/') === 0) {
        $full = __DIR__ . '/' . $path;
        if (is_file($full)) {
            @unlink($full);
        }
    }
}

/* Records who did what, on which table/record, and when */
function log_activity(PDO $pdo, string $action, string $table, int $recordId, ?int $userId = null): void
{
    $userId = $userId ?? $_SESSION['user_id'];
    $stmt = $pdo->prepare(
        'INSERT INTO activity_logs (user_id, action, table_name, record_id) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $action, $table, $recordId]);
}

/* ---------- Layout ---------- */

function page_header(string $title, bool $showNav = true, string $active = ''): void
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · The Kape Project</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
<?php if ($showNav && is_logged_in()): ?>
    <header class="navbar">
        <div class="navbar-inner">
            <a class="brand" href="<?= BASE_URL ?>/index.php">The Kape Project</a>
            <nav class="nav-links">
                <a href="<?= BASE_URL ?>/index.php" class="<?= $active === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
                <a href="<?= BASE_URL ?>/customers/view_customers.php" class="<?= $active === 'customers' ? 'active' : '' ?>">Customers</a>
                <a href="<?= BASE_URL ?>/orders/view_orders.php" class="<?= $active === 'orders' ? 'active' : '' ?>">Orders</a>
            </nav>
            <div class="nav-user">
                <span><?= e($_SESSION['user_name']) ?></span>
                <a href="<?= BASE_URL ?>/users/logout.php" onclick="return confirm('Are you sure you want to log out?');">Log out</a>
            </div>
        </div>
    </header>
    <main class="container">
<?php else: ?>
    <main class="auth-wrap">
<?php endif; ?>
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
<?php
}

function page_footer(): void
{
    echo "</main>\n</body>\n</html>";
}
