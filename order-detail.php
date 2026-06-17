<?php
session_start();
require_once 'php/auth_session.php';
requireCustomer('../login.html');
require_once 'php/db_connect.php';

$oid = (int)($_GET['id'] ?? 0);
$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii', $oid, $uid);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) { header('Location: my-orders.php'); exit; }

$items = $conn->query("SELECT * FROM order_items WHERE order_id = $oid")->fetch_all(MYSQLI_ASSOC);
$conn->close();

$statusColors = ['pending'=>'#E67E22','confirmed'=>'#2980B9','shipped'=>'#8E44AD','delivered'=>'#27AE60','cancelled'=>'#C0392B'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Order #<?= $oid ?> — Umutako Art Store</title>
    <link rel="stylesheet" href="css/style.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
</head>
<body>

<nav id="navbar">
    <div class="nav-inner">
        <a href="index.html" class="logo">🎨 Umutako <span>Art Store</span></a>
        <ul class="nav-links" id="nav-links">
            <li><a href="index.html#products">Shop</a></li>
            <li><a href="my-orders.php">My Orders</a></li>
            <li><a href="php/logout.php" style="color:#C0392B">Logout</a></li>
        </ul>
        <button class="hamburger" onclick="toggleNav()"><span></span><span></span><span></span></button>
    </div>
</nav>

<div class="admin-wrap">
    <div class="admin-page-header">
        <h2>Order #<?= $oid ?></h2>
        <a href="my-orders.php" class="btn-secondary" style="font-size:.9rem;padding:10px 22px;border-color:var(--cream-d);color:var(--text-600)">← Back to Orders</a>
    </div>

    <div class="detail-grid">
        <div class="detail-card">
            <h4>Order Info</h4>
            <p><strong>Date:</strong> <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
            <p><strong>Status:</strong> <span class="status-badge" style="background:<?= $statusColors[$order['status']] ?>"><?= ucfirst($order['status']) ?></span></p>
            <p><strong>Total:</strong> RWF <?= number_format($order['total_amount']) ?></p>
            <?php if ($order['notes']): ?><p><strong>Notes:</strong> <?= htmlspecialchars($order['notes']) ?></p><?php endif; ?>
        </div>
        <div class="detail-card">
            <h4>Delivery Address</h4>
            <p><?= htmlspecialchars($order['customer_address']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone'] ?: 'N/A') ?></p>
        </div>
    </div>

    <div class="orders-table-wrap" style="margin-top:24px">
        <h4 style="padding:0 0 14px;font-family:'Playfair Display',serif;color:var(--choc-800)">Items Ordered</h4>
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
            <tfoot>
                <tr><td colspan="3"><strong>Total</strong></td><td><strong>RWF <?= number_format($order['total_amount']) ?></strong></td></tr>
            </tfoot>
        </table>
    </div>
</div>

<script>function toggleNav(){document.getElementById('nav-links')?.classList.toggle('open');}</script>
</body>
</html>
