<?php
require_once __DIR__ . '/../auth.php';
require_login();

// CHANGED: read the search term
$search = trim($_GET['search'] ?? '');

// CHANGED: select date_added and last_updated from the customers table
$sql = "SELECT c.customer_id, c.customer_name, c.contact_number, c.date_added,
               CONCAT(a.first_name, ' ', a.last_name) AS added_by_name,
               CONCAT(l.first_name, ' ', l.last_name) AS updated_by_name,
               COALESCE(c.last_updated,
                   (SELECT MAX(al.action_time) FROM activity_logs al
                     WHERE al.table_name = 'customers'
                       AND al.record_id = c.customer_id
                       AND al.action IN ('INSERT', 'UPDATE'))) AS last_updated_at
        FROM customers c
        LEFT JOIN users a ON a.user_id = c.added_by
        LEFT JOIN users l ON l.user_id = c.last_updated_by";

// CHANGED: add the search condition (prepared statement, safe from SQL injection)
$params = [];
if ($search !== '') {
    $sql .= " WHERE c.customer_name LIKE ? OR c.contact_number LIKE ?";
    $params = ["%$search%", "%$search%"];
}
$sql .= " ORDER BY c.customer_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();

page_header('Customers', true, 'customers');
?>
<div class="page-head">
    <div>
        <h1>Customers</h1>
        <p class="muted"><?= count($customers) ?> record(s)</p>
    </div>
    <a href="add_customers.php" class="btn btn-primary">Add customer</a>
</div>

<!-- CHANGED: search form -->
<section class="panel" style="margin-bottom:16px; padding:16px;">
    <form method="get" action="view_customers.php" style="display:flex; gap:8px; align-items:center;">
        <input type="text" name="search" value="<?= e($search) ?>"
               placeholder="Search customer name or contact..." style="flex:1;">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($search !== ''): ?>
            <a href="view_customers.php" class="btn btn-ghost">Reset</a>
        <?php endif; ?>
    </form>
</section>

<section class="panel">
    <?php if (!$customers): ?>
        <!-- CHANGED: different message when a search finds nothing -->
        <p class="muted">
            <?= $search !== '' ? 'No customers match "' . e($search) . '".' : 'No customers yet. Add your first one.' ?>
        </p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Contact</th>
                    <th>Added by</th><th>Date added</th><th>Last updated by</th><th>Last updated</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($customers as $c): ?>
                <tr>
                    <td>#<?= (int) $c['customer_id'] ?></td>
                    <td><strong><?= e($c['customer_name']) ?></strong></td>
                    <td><?= e($c['contact_number'] ?: '—') ?></td>
                    <td><?= e($c['added_by_name'] ?? '—') ?></td>
                    <!-- CHANGED: date_added column -->
                    <td><?= e($c['date_added'] ? format_time($c['date_added']) : '—') ?></td>
                    <td><?= e($c['updated_by_name'] ?? '—') ?></td>
                    <td><?= e($c['last_updated_at'] ? format_time($c['last_updated_at']) : '—') ?></td>
                    <td class="actions">
                        <a href="edit_customers.php?id=<?= (int) $c['customer_id'] ?>">Edit</a>
                        <form method="post" action="delete_customers.php"
                              onsubmit="return confirm('Delete this customer?');">
                            <input type="hidden" name="id" value="<?= (int) $c['customer_id'] ?>">
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
