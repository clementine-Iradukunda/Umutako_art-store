<?php
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require_once '_header.php';
require_once '../php/db_connect.php';

$stats = [];
$stats['orders']    = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$stats['products']  = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$stats['customers'] = $conn->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetch_row()[0];
$stats['revenue']   = $conn->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status != 'cancelled'")->fetch_row()[0];
$stats['pending']   = $conn->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetch_row()[0];

$recent = $conn->query(
    "SELECT o.id, o.customer_name, o.total_amount, o.status, o.created_at
     FROM orders o ORDER BY o.created_at DESC LIMIT 8"
)->fetch_all(MYSQLI_ASSOC);

$conn->close();

$statusColors = ['pending'=>'#E67E22','confirmed'=>'#2980B9','shipped'=>'#8E44AD','delivered'=>'#27AE60','cancelled'=>'#C0392B'];
?>

<div class="admin-page-header">
    <h2>📊 Dashboard</h2>
    <span class="header-date"><?= date('l, d F Y') ?></span>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
            <div class="stat-num"><?= $stats['orders'] ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
            <div class="stat-num"><?= $stats['pending'] ?></div>
            <div class="stat-label">Pending Orders</div>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">💰</div>
        <div class="stat-info">
            <div class="stat-num">RWF <?= number_format($stats['revenue']) ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">🛍️</div>
        <div class="stat-info">
            <div class="stat-num"><?= $stats['products'] ?></div>
            <div class="stat-label">Products</div>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <div class="stat-num"><?= $stats['customers'] ?></div>
            <div class="stat-label">Customers</div>
        </div>
    </div>
</div>

<div class="admin-section-title">Recent Orders</div>
<div class="table-wrap">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($recent as $o): ?>
        <tr>
            <td><strong>#<?= $o['id'] ?></strong></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td>RWF <?= number_format($o['total_amount']) ?></td>
            <td><span class="status-badge" style="background:<?= $statusColors[$o['status']] ?>"><?= ucfirst($o['status']) ?></span></td>
            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
            <td><a href="orders.php?view=<?= $o['id'] ?>" class="view-link">View →</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '_footer.php'; ?>
