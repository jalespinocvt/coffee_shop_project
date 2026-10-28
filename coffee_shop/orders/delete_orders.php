<?php
require_once __DIR__ . '/../auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('orders/view_orders.php');
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $stmt = $pdo->prepare('DELETE FROM orders WHERE order_id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        log_activity($pdo, 'DELETE', 'orders', $id);
        flash('success', 'Order deleted.');
    } else {
        flash('error', 'Order not found.');
    }
} catch (PDOException $e) {
    flash('error', 'Unable to delete this order.');
}

redirect('orders/view_orders.php');
