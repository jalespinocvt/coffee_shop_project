<?php
require_once __DIR__ . '/../auth.php';
require_login();

$customers = $pdo->query('SELECT customer_id, customer_name FROM customers ORDER BY customer_name')->fetchAll();

$errors = [];
$orderDate  = date('Y-m-d');
$product    = '';
$customerId = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderDate  = $_POST['order_date'] ?? '';
    $product    = trim($_POST['product_name'] ?? '');
    $customerId = (int) ($_POST['customer_id'] ?? 0);

    $d = DateTime::createFromFormat('Y-m-d', $orderDate);
    if (!$d || $d->format('Y-m-d') !== $orderDate) $errors[] = 'Enter a valid order date.';
    if ($product === '') $errors[] = 'Product name is required.';
    if (strlen($product) > 100) $errors[] = 'Product name is too long.';
    if ($customerId <= 0) $errors[] = 'Select a customer.';

    // CHANGED: handle the image upload (required when adding)
    $imagePath = null;
    if (!$errors) {
        [$imagePath, $imgError] = upload_image($_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
        if ($imgError) {
            $errors[] = $imgError;
        } elseif ($imagePath === null) {
            $errors[] = 'Please attach an image for this order.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO orders (order_date, product_name, image_path, customer_id, added_by, last_updated_by)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$orderDate, $product, $imagePath, $customerId, $_SESSION['user_id'], $_SESSION['user_id']]);
        log_activity($pdo, 'INSERT', 'orders', (int) $pdo->lastInsertId());

        flash('success', 'Order added successfully.');
        redirect('orders/view_orders.php');
    }
}

page_header('Add order', true, 'orders');
?>
<div class="page-head">
    <h1>Add order</h1>
</div>

<section class="panel form-panel">
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <?php if (!$customers): ?>
        <p class="muted">You need at least one customer before adding an order.
            <a href="<?= BASE_URL ?>/customers/add_customers.php">Add a customer</a></p>
    <?php else: ?>
    <!-- CHANGED: enctype is required for file uploads -->
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="order_date">Order date</label>
            <input type="date" id="order_date" name="order_date" value="<?= e($orderDate) ?>" required>
        </div>
        <div class="form-group">
            <label for="product_name">Product</label>
            <input type="text" id="product_name" name="product_name" value="<?= e($product) ?>"
                   placeholder="e.g. Caramel Macchiato" required>
        </div>
        <div class="form-group">
            <label for="customer_id">Customer</label>
            <select id="customer_id" name="customer_id" required>
                <option value="">Select a customer</option>
                <?php foreach ($customers as $c): ?>
                    <option value="<?= (int) $c['customer_id'] ?>"
                        <?= (int) $customerId === (int) $c['customer_id'] ? 'selected' : '' ?>>
                        <?= e($c['customer_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- CHANGED: image field -->
        <div class="form-group">
            <label for="image">Order image <span class="optional">(JPG, PNG, GIF or WEBP, max 2 MB)</span></label>
            <input type="file" id="image" name="image" accept="image/*" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save order</button>
            <a href="view_orders.php" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
    <?php endif; ?>
</section>
<?php page_footer(); ?>
