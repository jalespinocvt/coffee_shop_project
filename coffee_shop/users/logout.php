<?php
require_once __DIR__ . '/../auth.php';

if (is_logged_in()) {
    log_activity($pdo, 'LOGOUT', 'users', (int) $_SESSION['user_id']);
}

$_SESSION = [];
session_destroy();

session_start();
flash('success', 'You have been logged out.');
redirect('users/login.php');
