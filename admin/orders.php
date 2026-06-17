<?php
$pageTitle  = 'Orders';
$activePage = 'orders';
require_once '_header.php';
require_once '../php/db_connect.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $allowed = ['pending','confirmed','shipped','delivered','cancelled'];
    $status  = in_array($_POST['status'], $allowed) ? $_POST['status'] : 'pending';
    $oid     = (int)$_POST['order_id'];
    $conn->query("UPDATE orders SET status='$status' WHERE id=$oid");
    header('Location: orders.php?updated=1'); exit;
}

$statusColors = ['pending'=>'#E67E22','confirmed'=>'#2980B9','shipped'=>'#8E44AD','delivered'=>'#27AE60','cancelled'=>'#C0392B'];

// View single order
if (isset($_GET['view'])) {
    $oid   = (int)$_GET['view'];
    $order = $conn->query("SELECT * FROM orders WHERE id=$oid")->fetch_assoc();
    $items = $conn->query("SELECT * FROM order_items WHERE order_id=$oid")->fetch_all(MYSQLI_ASSOC);
    if ($order): ?>

<div class="admin-page-header">
    <h2>Order #<?= $oid ?></h2>
    <a href="orders.php" class="admin-btn-secondary">← Back to Orders</a>
</div>

<div class="detail-grid">
    <div class="detail-card">
        <h4>Customer</h4>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone'] ?: 'N/A') ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['customer_address']) ?></p>
    </div>
    <div class="detail-card">
        <h4>Order Info</h4>
        <p><strong>Date:</strong> <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>Total:</strong> RWF <?= number_format($order['total_amount']) ?></p>
        <p><strong>Notes:</strong> <?= htmlspecialchars($order['notes'] ?: 'None') ?></p>
        <form method="POST" style="margin-top:14px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="hidden" name="order_id" value="<?= $oid ?>"/>
            <select name="status" class="admin-select">
                <?php foreach (['pending','confirmed','shipped','delivered','cancelled'] as $s): ?>
                <option value="<?=$s?>" <?=$order['status']===$s?'selected':''?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="admin-btn">Update Status</button>
        </form>
    </div>
</div>

<div class="table-wrap" style="margin-top:24px">
    <table class="admin-table">
        <thead><tr><th>Product</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td>RWF <?= number_format($item['unit_price']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>RWF <?= number_format($item['unit_price'] * $item['quantity']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><td colspan="3"><strong>Total</strong></td><td><strong>RWF <?= number_format($order['total_amount']) ?></strong></td></tr></tfoot>
    </table>
</div>

    <?php endif;
    $conn->close();
    require_once '_footer.php';
    exit;
}

// List all orders
$filter = $_GET['status'] ?? '';
$where  = $filter ? "WHERE o.status='".mysqli_real_escape_string($conn, $filter)."'" : '';
$orders = $conn->query("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id=oi.order_id $where GROUP BY o.id ORDER BY o.created_at DESC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<div class="admin-page-header">
    <h2>📦 Orders</h2>
    <?php if (isset($_GET['updated'])): ?><span class="success-flash">✅ Status updated!</span><?php endif; ?>
</div>

<div class="filter-bar">
    <?php foreach ([''=>'All','pending'=>'Pending','confirmed'=>'Confirmed','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $val=>$label): ?>
    <a href="orders.php<?= $val ? '?status='.$val : '' ?>" class="filter-btn <?= $filter===$val?'active':'' ?>"><?= $label ?></a>
    <?php endforeach; ?>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td><strong>#<?= $o['id'] ?></strong></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td><?= $o['item_count'] ?></td>
            <td>RWF <?= number_format($o['total_amount']) ?></td>
            <td><span class="status-badge" style="background:<?= $statusColors[$o['status']] ?>"><?= ucfirst($o['status']) ?></span></td>
            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
            <td><a href="orders.php?view=<?= $o['id'] ?>" class="view-link">View →</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?><tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-400)">No orders found.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '_footer.php'; ?>
