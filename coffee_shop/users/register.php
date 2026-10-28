<?php
require_once __DIR__ . '/../auth.php';
redirect_if_logged_in();

$errors = [];
$data = ['first_name' => '', 'last_name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['first_name'] = trim($_POST['first_name'] ?? '');
    $data['last_name']  = trim($_POST['last_name'] ?? '');
    $data['email']      = trim($_POST['email'] ?? '');
    $password           = $_POST['password'] ?? '';
    $confirm            = $_POST['confirm_password'] ?? '';

    if ($data['first_name'] === '') $errors[] = 'First name is required.';
    if ($data['last_name'] === '')  $errors[] = 'Last name is required.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 6)      $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm)     $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $check = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $check->execute([$data['email']]);
        if ($check->fetch()) {
            $errors[] = 'That email is already registered.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            password_hash($password, PASSWORD_DEFAULT),
        ]);
        $newId = (int) $pdo->lastInsertId();
        log_activity($pdo, 'REGISTER', 'users', $newId, $newId);

        flash('success', 'Account created. You can now log in.');
        redirect('users/login.php');
    }
}

page_header('Register', false);
?>
<div class="auth-card">
    <div class="auth-brand">The Kape Project</div>

    <h1>Create account</h1>
    <p class="muted">Register to manage The Kape Project.</p>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form method="post" novalidate>
        <div class="form-row">
            <div class="form-group">
                <label for="first_name">First name</label>
                <input type="text" id="first_name" name="first_name" value="<?= e($data['first_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last name</label>
                <input type="text" id="last_name" name="last_name" value="<?= e($data['last_name']) ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($data['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>

    <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
</div>
<?php page_footer(); ?>
