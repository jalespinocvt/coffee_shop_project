
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /coffee_shop_project/coffee_shop/login.php');
    exit;
}
