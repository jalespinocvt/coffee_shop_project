<?php
require_once __DIR__ . '/../auth.php';
redirect_if_logged_in();

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']    = (int) $user['user_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['user_name']  = $user['first_name'] . ' ' . $user['last_name'];

        log_activity($pdo, 'LOGIN', 'users', (int) $user['user_id']);
        redirect('index.php');
    }
    $error = 'Incorrect email or password.';
}

page_header('Log in', false);
?>
<div class="auth-card">
    <div class="auth-brand">The Kape Project</div>

    <h1>Welcome back</h1>
    <p class="muted">Log in to continue.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Log in</button>
    </form>

    <p class="auth-switch">No account yet? <a href="register.php">Register</a></p>
</div>
<?php page_footer(); ?>
