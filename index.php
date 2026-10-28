<?php
require_once __DIR__ . '/auth.php';
require_login();

$totalCustomers = $pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn();
$totalOrders    = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();

$recent = $pdo->query(
    "SELECT l.action, l.table_name, l.record_id, l.action_time,
            CONCAT(u.first_name, ' ', u.last_name) AS user_name
     FROM activity_logs l
     LEFT JOIN users u ON u.user_id = l.user_id
     ORDER BY l.action_time DESC, l.log_id DESC
     LIMIT 8"
)->fetchAll();

page_header('Dashboard', true, 'dashboard');
?>
<div class="page-head">
    <div>
        <h1>Welcome back, <?= e($_SESSION['first_name']) ?></h1>
        <p class="muted">Here is what is happening at The Kape Project.</p>
    </div>
</div>

<div class="card-grid">
    <a class="stat-card" href="<?= BASE_URL ?>/customers/view_customers.php">
        <span class="stat-label">Customers</span>
        <span class="stat-number"><?= (int) $totalCustomers ?></span>
        <span class="stat-link">Manage customers →</span>
    </a>
    <a class="stat-card" href="<?= BASE_URL ?>/orders/view_orders.php">
        <span class="stat-label">Orders</span>
        <span class="stat-number"><?= (int) $totalOrders ?></span>
        <span class="stat-link">Manage orders →</span>
    </a>
</div>

<section class="panel">
    <h2>Recent activity</h2>
    <?php if (!$recent): ?>
        <p class="muted">No activity yet.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>User</th><th>Action</th><th>Table</th><th>Record</th><th>When</th></tr>
            </thead>
            <tbody>
            <?php foreach ($recent as $row): ?>
                <tr>
                    <td><?= e($row['user_name'] ?? 'Unknown') ?></td>
                    <td><span class="badge badge-<?= strtolower(e($row['action'])) ?>"><?= e($row['action']) ?></span></td>
                    <td><?= e($row['table_name']) ?></td>
                    <td>#<?= (int) $row['record_id'] ?></td>
                    <td><?= e(format_time($row['action_time'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>
<?php page_footer(); ?>
