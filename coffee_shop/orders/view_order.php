<?php
require_once __DIR__ . '/../auth.php';
require_login();

$search = trim($_GET['search'] ?? '');

// CHANGED: o.image_path added to the SELECT
$sql = "SELECT o.order_id, o.order_date, o.product_name, o.image_path, c.customer_name,
            CONCAT(a.first_name, ' ', a.last_name) AS added_by_name,
            CONCAT(l.first_name, ' ', l.last_name) AS updated_by_name,
            (SELECT MAX(al.action_time) FROM activity_logs al
              WHERE al.table_name = 'orders'
                AND al.record_id = o.order_id
                AND al.action IN ('INSERT', 'UPDATE')) AS last_updated_at
     FROM orders o
     LEFT JOIN customers c ON c.customer_id = o.customer_id
     LEFT JOIN users a ON a.user_id = o.added_by
     LEFT JOIN users l ON l.user_id = o.last_updated_by";

$sql .= " WHERE o.is_deleted = 0";
$params = [];
if ($search !== '') {
    $sql .= " AND (o.product_name LIKE ? OR c.customer_name LIKE ?)";
    $params = ["%$search%", "%$search%"];
}
$sql .= " ORDER BY o.order_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$binCount = (int) $pdo->query('SELECT COUNT(*) FROM orders WHERE is_deleted = 1')->fetchColumn();

page_header('Orders', true, 'orders');
?>
<div class="page-head">
    <div>
        <h1>Orders</h1>
        <p class="muted"><?= count($orders) ?> record(s)</p>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="recycle_bin_orders.php" class="btn btn-ghost">Recycle bin (<?= $binCount ?>)</a>
        <a href="add_orders.php" class="btn btn-primary">Add order</a>
    </div>
</div>

<section class="panel" style="margin-bottom:16px; padding:16px;">
    <form method="get" action="view_orders.php" style="display:flex; gap:8px; align-items:center;">
        <input type="text" name="search" value="<?= e($search) ?>"
               placeholder="Search product or customer..." style="flex:1;">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($search !== ''): ?>
            <a href="view_orders.php" class="btn btn-ghost">Reset</a>
        <?php endif; ?>
    </form>
</section>

<section class="panel">
    <?php if (!$orders): ?>
        <p class="muted">
            <?= $search !== '' ? 'No orders match "' . e($search) . '".' : 'No orders yet. Add your first one.' ?>
        </p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Image</th><th>Date</th><th>Product</th><th>Customer</th>
                    <th>Added by</th><th>Last updated by</th><th>Last updated</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= (int) $o['order_id'] ?></td>
                    <!-- CHANGED: image thumbnail column -->
                    <td>
                        <?php if ($o['image_path']): ?>
                            <a href="<?= BASE_URL ?>/<?= e($o['image_path']) ?>" target="_blank">
                                <img src="<?= BASE_URL ?>/<?= e($o['image_path']) ?>" alt="Order image" class="thumb">
                            </a>
                        <?php else: ?>
                            <span class="muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($o['order_date'] ? date('M j, Y', strtotime($o['order_date'])) : '—') ?></td>
                    <td><strong><?= e($o['product_name']) ?></strong></td>
                    <td><?= e($o['customer_name'] ?? '—') ?></td>
                    <td><?= e($o['added_by_name'] ?? '—') ?></td>
                    <td><?= e($o['updated_by_name'] ?? '—') ?></td>
                    <td><?= e(format_time($o['last_updated_at'])) ?></td>
                    <td class="actions">
                        <a href="edit_orders.php?id=<?= (int) $o['order_id'] ?>">Edit</a>
                        <form method="post" action="delete_orders.php"
                              onsubmit="return confirm('Move this order to the recycle bin?');">
                            <input type="hidden" name="id" value="<?= (int) $o['order_id'] ?>">
                            <button type="submit" class="link-danger">Delete</button>
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
