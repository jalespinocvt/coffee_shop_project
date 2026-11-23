<?php
require_once __DIR__ . '/../auth.php';
require_login();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

// CHANGED: deleted (recycle bin) orders can't be edited
$stmt = $pdo->prepare('SELECT * FROM orders WHERE order_id = ? AND is_deleted = 0');
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    flash('error', 'Order not found.');
    redirect('orders/view_orders.php');
}

$customers = $pdo->query('SELECT customer_id, customer_name FROM customers ORDER BY customer_name')->fetchAll();

$errors = [];
$orderDate  = $order['order_date'];
$product    = $order['product_name'];
$customerId = $order['customer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderDate  = $_POST['order_date'] ?? '';
    $product    = trim($_POST['product_name'] ?? '');
    $customerId = (int) ($_POST['customer_id'] ?? 0);

    $d = DateTime::createFromFormat('Y-m-d', $orderDate);
    if (!$d || $d->format('Y-m-d') !== $orderDate) $errors[] = 'Enter a valid order date.';
    if ($product === '') $errors[] = 'Product name is required.';
    if (strlen($product) > 100) $errors[] = 'Product name is too long.';
    if ($customerId <= 0) $errors[] = 'Select a customer.';

    // CHANGED: optional new image
    $newImage = null;
    if (!$errors) {
        [$newImage, $imgError] = upload_image($_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
        if ($imgError) $errors[] = $imgError;
    }

    if (!$errors) {
        // keep the old image unless a new one was uploaded
        $imagePath = $newImage ?? $order['image_path'];

        $update = $pdo->prepare(
            'UPDATE orders
             SET order_date = ?, product_name = ?, image_path = ?, customer_id = ?, last_updated_by = ?
             WHERE order_id = ?'
        );
        $update->execute([$orderDate, $product, $imagePath, $customerId, $_SESSION['user_id'], $id]);
        log_activity($pdo, 'UPDATE', 'orders', $id);

        // CHANGED: remove the replaced image file
        if ($newImage !== null) {
            delete_image_file($order['image_path']);
        }

        flash('success', 'Order updated successfully.');
        redirect('orders/view_orders.php');
    }
}

page_header('Edit order', true, 'orders');
?>
<div class="page-head">
    <h1>Edit order #<?= $id ?></h1>
</div>

<section class="panel form-panel">
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <!-- CHANGED: enctype is required for file uploads -->
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="form-group">
            <label for="order_date">Order date</label>
            <input type="date" id="order_date" name="order_date" value="<?= e($orderDate) ?>" required>
        </div>
        <div class="form-group">
            <label for="product_name">Product</label>
            <input type="text" id="product_name" name="product_name" value="<?= e($product) ?>" required>
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
        <!-- CHANGED: current image + replace field -->
        <div class="form-group">
            <label>Current image</label>
            <?php if ($order['image_path']): ?>
                <img src="<?= BASE_URL ?>/<?= e($order['image_path']) ?>" alt="Order image" class="thumb-preview">
            <?php else: ?>
                <p class="muted">No image yet.</p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="image">Replace image <span class="optional">(leave empty to keep the current one)</span></label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update order</button>
            <a href="view_orders.php" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</section>
<?php page_footer(); ?>
