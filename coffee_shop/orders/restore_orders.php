<?php
require_once __DIR__ . '/../auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('orders/recycle_bin_orders.php');
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $stmt = $pdo->prepare(
        'UPDATE orders
            SET is_deleted = 0, deleted_by = NULL, deleted_at = NULL
          WHERE order_id = ? AND is_deleted = 1'
    );
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        log_activity($pdo, 'RESTORE', 'orders', $id);
        flash('success', 'Order restored.');
        redirect('orders/view_orders.php');
    } else {
        flash('error', 'Order not found in the recycle bin.');
    }
} catch (PDOException $e) {
    flash('error', 'Unable to restore this order.');
}

redirect('orders/recycle_bin_orders.php');
