<?php
require_once __DIR__ . '/../auth.php';
require_login();

$errors = [];
$name = '';
$contact = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['customer_name'] ?? '');
    $contact = trim($_POST['contact_number'] ?? '');

    if ($name === '') $errors[] = 'Customer name is required.';
    if (strlen($name) > 100) $errors[] = 'Customer name is too long.';
    if ($contact !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $contact)) {
        $errors[] = 'Enter a valid contact number.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO customers (customer_name, contact_number, added_by, last_updated_by)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $name,
            $contact !== '' ? $contact : null,
            $_SESSION['user_id'],
            $_SESSION['user_id'],
        ]);
        log_activity($pdo, 'INSERT', 'customers', (int) $pdo->lastInsertId());

        flash('success', 'Customer added successfully.');
        redirect('customers/view_customers.php');
    }
}

page_header('Add customer', true, 'customers');
?>
<div class="page-head">
    <h1>Add customer</h1>
</div>

<section class="panel form-panel">
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form method="post">
        <div class="form-group">
            <label for="customer_name">Customer name</label>
            <input type="text" id="customer_name" name="customer_name" value="<?= e($name) ?>" required autofocus>
        </div>
        <div class="form-group">
            <label for="contact_number">Contact number <span class="optional">(optional)</span></label>
            <input type="text" id="contact_number" name="contact_number" value="<?= e($contact) ?>">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save customer</button>
            <a href="view_customers.php" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</section>
<?php page_footer(); ?>
