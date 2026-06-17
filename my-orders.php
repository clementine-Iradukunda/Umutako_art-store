<?php
session_start();
require_once 'php/auth_session.php';
requireCustomer('../login.html');
require_once 'php/db_connect.php';

$uid  = $_SESSION['user_id'];
$stmt = $conn->prepare(
    "SELECT o.*, COUNT(oi.id) as item_count
     FROM orders o
     LEFT JOIN order_items oi ON o.id = oi.order_id
     WHERE o.user_id = ?
     GROUP BY o.id
     ORDER BY o.created_at DESC"
);
$stmt->bind_param('i', $uid);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$conn->close();

$statusColors = [
    'pending'   => '#E67E22',
    'confirmed' => '#2980B9',
    'shipped'   => '#8E44AD',
    'delivered' => '#27AE60',
    'cancelled' => '#C0392B',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>My Orders — Umutako Art Store</title>
    <link rel="stylesheet" href="css/style.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
</head>
<body>

<nav id="navbar">
    <div class="nav-inner">
        <a href="index.html" class="logo">🎨 Umutako <span>Art Store</span></a>
        <ul class="nav-links" id="nav-links">
            <li><a href="index.html#products">Shop</a></li>
            <li><a href="my-orders.php" style="color:var(--gold-500)">My Orders</a></li>
            <li><a href="php/logout.php" style="color:#C0392B">Logout (<?= htmlspecialchars($_SESSION['name']) ?>)</a></li>
        </ul>
        <button class="hamburger" onclick="toggleNav()"><span></span><span></span><span></span></button>
    </div>
</nav>

<div class="admin-wrap">
    <div class="admin-page-header">
        <h2>🛍️ My Orders</h2>
        <a href="index.html#products" class="btn-primary" style="font-size:.9rem;padding:10px 22px">Continue Shopping</a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <div style="font-size:4rem">🛒</div>
            <h3>No orders yet</h3>
            <p>You haven't placed any orders. Start shopping!</p>
            <a href="index.html#products" class="btn-primary">Shop Now</a>
        </div>
    <?php else: ?>
        <div class="orders-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><strong>#<?= $o['id'] ?></strong></td>
                        <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                        <td><?= $o['item_count'] ?> item(s)</td>
                        <td>RWF <?= number_format($o['total_amount']) ?></td>
                        <td>
                            <span class="status-badge" style="background:<?= $statusColors[$o['status']] ?? '#888' ?>">
                                <?= ucfirst($o['status']) ?>
                            </span>
                        </td>
                        <td><a href="order-detail.php?id=<?= $o['id'] ?>" class="view-link">View →</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>function toggleNav(){document.getElementById('nav-links')?.classList.toggle('open');}</script>
</body>
</html>
