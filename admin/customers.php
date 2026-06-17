<?php
$pageTitle  = 'Customers';
$activePage = 'customers';
require_once '_header.php';
require_once '../php/db_connect.php';

$customers = $conn->query(
    "SELECT u.*, COUNT(o.id) as order_count, COALESCE(SUM(o.total_amount),0) as total_spent
     FROM users u
     LEFT JOIN orders o ON o.customer_email = u.email
     WHERE u.role='customer'
     GROUP BY u.id
     ORDER BY u.created_at DESC"
)->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<div class="admin-page-header">
    <h2>👥 Customers</h2>
    <span class="header-date"><?= count($customers) ?> registered</span>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Total Spent</th><th>Joined</th></tr></thead>
        <tbody>
        <?php foreach ($customers as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['phone'] ?: '—') ?></td>
            <td><?= $c['order_count'] ?></td>
            <td>RWF <?= number_format($c['total_spent']) ?></td>
            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($customers)): ?>
        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-400)">No customers yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '_footer.php'; ?>
