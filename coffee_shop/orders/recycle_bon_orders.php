<?php
require_once __DIR__ . '/../auth.php';
require_login();

$sql = "SELECT o.order_id, o.order_date, o.product_name, o.image_path, c.customer_name,
            CONCAT(d.first_name, ' ', d.last_name) AS deleted_by_name,
            o.deleted_at
     FROM orders o
     LEFT JOIN customers c ON c.customer_id = o.customer_id
     LEFT JOIN users d ON d.user_id = o.deleted_by
     WHERE o.is_deleted = 1
     ORDER BY o.deleted_at DESC";
$deleted = $pdo->query($sql)->fetchAll();

page_header('Orders Recycle Bin', true, 'orders');
?>
<div class="page-head">
    <div>
        <h1>Orders recycle bin</h1>
        <p class="muted"><?= count($deleted) ?> deleted record(s)</p>
    </div>
    <a href="view_orders.php" class="btn btn-ghost">Back to orders</a>
</div>

<section class="panel">
    <?php if (!$deleted): ?>
        <p class="muted">The recycle bin is empty.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Image</th><th>Date</th><th>Product</th><th>Customer</th>
                    <th>Deleted by</th><th>Deleted at</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($deleted as $o): ?>
                <tr>
                    <td>#<?= (int) $o['order_id'] ?></td>
                    <td>
                        <?php if ($o['image_path']): ?>
                            <img src="<?= BASE_URL ?>/<?= e($o['image_path']) ?>" alt="Order image" class="thumb">
                        <?php else: ?>
                            <span class="muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($o['order_date'] ? date('M j, Y', strtotime($o['order_date'])) : '—') ?></td>
                    <td><strong><?= e($o['product_name']) ?></strong></td>
                    <td><?= e($o['customer_name'] ?? '—') ?></td>
                    <td><?= e($o['deleted_by_name'] ?? '—') ?></td>
                    <td><?= e(format_time($o['deleted_at'])) ?></td>
                    <td class="actions">
                        <form method="post" action="restore_orders.php"
                              onsubmit="return confirm('Restore this order?');">
                            <input type="hidden" name="id" value="<?= (int) $o['order_id'] ?>">
                            <button type="submit" class="btn btn-primary">Restore</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>
<?php page_footer(); ?>
