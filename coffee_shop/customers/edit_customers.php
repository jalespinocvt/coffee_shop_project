<?php
require_once __DIR__ . '/../auth.php';
require_login();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM customers WHERE customer_id = ?');
$stmt->execute([$id]);
$customer = $stmt->fetch();

if (!$customer) {
    flash('error', 'Customer not found.');
    redirect('customers/view_customers.php');
}

$errors = [];
$name = $customer['customer_name'];
$contact = $customer['contact_number'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['customer_name'] ?? '');
    $contact = trim($_POST['contact_number'] ?? '');

    if ($name === '') $errors[] = 'Customer name is required.';
    if (strlen($name) > 100) $errors[] = 'Customer name is too long.';
    if ($contact !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $contact)) {
        $errors[] = 'Enter a valid contact number.';
    }

    if (!$errors) {
        $update = $pdo->prepare(
            'UPDATE customers
             SET customer_name = ?, contact_number = ?, last_updated_by = ?
             WHERE customer_id = ?'
        );
        $update->execute([
            $name,
            $contact !== '' ? $contact : null,
            $_SESSION['user_id'],
            $id,
        ]);
        log_activity($pdo, 'UPDATE', 'customers', $id);

        flash('success', 'Customer updated successfully.');
        redirect('customers/view_customers.php');
    }
}

page_header('Edit customer', true, 'customers');
?>
<div class="page-head">
    <h1>Edit customer #<?= $id ?></h1>
</div>

<section class="panel form-panel">
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="form-group">
            <label for="customer_name">Customer name</label>
            <input type="text" id="customer_name" name="customer_name" value="<?= e($name) ?>" required>
        </div>
        <div class="form-group">
            <label for="contact_number">Contact number <span class="optional">(optional)</span></label>
            <input type="text" id="contact_number" name="contact_number" value="<?= e($contact) ?>">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update customer</button>
            <a href="view_customers.php" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</section>
<?php page_footer(); ?>
