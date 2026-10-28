<?php
require_once __DIR__ . '/../auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('customers/view_customers.php');
}

$id = (int) ($_POST['id'] ?? 0);

try {
    $stmt = $pdo->prepare('DELETE FROM customers WHERE customer_id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        log_activity($pdo, 'DELETE', 'customers', $id);
        flash('success', 'Customer deleted.');
    } else {
        flash('error', 'Customer not found.');
    }
} catch (PDOException $e) {
    // Foreign key: the customer still has orders
    flash('error', 'This customer still has orders. Delete those orders first.');
}

redirect('customers/view_customers.php');
