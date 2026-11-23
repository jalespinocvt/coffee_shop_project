<?php
require_once __DIR__ . '/../auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('orders/view_orders.php');
}

$id = (int) ($_POST['id'] ?? 0);

try {
    // CHANGED: soft delete. The row stays in the table, but is flagged
    // and stamped with who deleted it and when.
    $stmt = $pdo->prepare(
        'UPDATE orders
            SET is_deleted = 1, deleted_by = ?, deleted_at = ?
          WHERE order_id = ? AND is_deleted = 0'
    );
    $stmt->execute([$_SESSION['user_id'], date('Y-m-d H:i:s'), $id]);

    if ($stmt->rowCount() > 0) {
        log_activity($pdo, 'DELETE', 'orders', $id);
        flash('success', 'Order moved to the recycle bin.');
    } else {
        flash('error', 'Order not found.');
    }
} catch (PDOException $e) {
    flash('error', 'Unable to delete this order.');
}

redirect('orders/view_orders.php');
