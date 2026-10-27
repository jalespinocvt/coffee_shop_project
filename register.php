
<?php
session_start();
require_once __DIR__ . '/config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
        $error = 'Please complete all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $check = mysqli_prepare(
            $conn,
            'SELECT user_id FROM users WHERE email = ?'
        );
        mysqli_stmt_bind_param($check, 's', $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'That email is already registered.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO users (first_name, last_name, email, password)
                 VALUES (?, ?, ?, ?)'
            );

            mysqli_stmt_bind_param(
                $stmt,
                'ssss',
                $firstName,
                $lastName,
                $email,
                $hashedPassword
            );

            if (mysqli_stmt_execute($stmt)) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | Coffee Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <nav class="navbar">
        <a class="brand" href="index.php">coffee<span>shop.</span></a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="login.php">Log in</a>
        </div>
    </nav>
</header>

<main class="container page-content">
    <p class="eyebrow">Join the team</p>
    <h1>Create an account</h1>
    <p>Register to access the Coffee Shop management system.</p>

    <?php if ($error !== ''): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <div class="form-group">
            <label for="first_name">First name</label>
            <input id="first_name" name="first_name" type="text"
                   maxlength="100" required
                   value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="last_name">Last name</label>
            <input id="last_name" name="last_name" type="text"
                   maxlength="100" required
                   value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email"
                   maxlength="150" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password">Password (at least 8 characters)</label>
            <input id="password" name="password" type="password"
                   minlength="8" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm password</label>
            <input id="confirm_password" name="confirm_password"
                   type="password" minlength="8" required>
        </div>

        <div class="form-actions">
            <button type="submit">Create account</button>
            <a class="btn btn-outline" href="login.php">I have an account</a>
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
