
<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if (isset($_GET['registered'])) {
    $success = 'Account created. You can now log in.';
} else {
    $success = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare(
        $conn,
        'SELECT user_id, first_name, last_name, password
         FROM users WHERE email = ?'
    );

    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result(
        $stmt,
        $userId,
        $firstName,
        $lastName,
        $hashedPassword
    );

    if (mysqli_stmt_fetch($stmt) &&
        password_verify($password, $hashedPassword)) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $userId;
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;

        mysqli_stmt_close($stmt);
        header('Location: index.php');
        exit;
    }

    mysqli_stmt_close($stmt);
    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In | Coffee Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <nav class="navbar">
        <a class="brand" href="index.php">coffee<span>shop.</span></a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
        </div>
    </nav>
</header>

<main class="container page-content">
    <p class="eyebrow">Welcome back</p>
    <h1>Log in</h1>
    <p>Sign in to manage your shop's records.</p>

    <?php if ($success !== ''): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email"
                   autocomplete="username" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password"
                   autocomplete="current-password" required>
        </div>

        <div class="form-actions">
            <button type="submit">Log in</button>
            <a class="btn btn-outline" href="register.php">Create account</a>
        </div>
    </form>
</main>

<footer class="site-footer">
    <div class="footer-inner">
        <p>coffee shop. — Customer &amp; order management</p>
    </div>
</footer>
</body>
</html>
