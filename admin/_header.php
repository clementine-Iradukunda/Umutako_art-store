<?php
require_once '../php/auth_session.php';
requireAdmin('../login.html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $pageTitle ?? 'Admin' ?> — Umutako Admin</title>
    <link rel="stylesheet" href="../css/style.css"/>
    <link rel="stylesheet" href="../css/admin.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
</head>
<body class="admin-body">

<aside class="sidebar">
    <div class="sidebar-logo">🎨 Umutako<br/><span>Admin Panel</span></div>
    <nav class="sidebar-nav">
        <a href="dashboard.php"  class="<?= ($activePage??'')==='dashboard'  ? 'active':'' ?>">📊 Dashboard</a>
        <a href="orders.php"     class="<?= ($activePage??'')==='orders'     ? 'active':'' ?>">📦 Orders</a>
        <a href="products.php"   class="<?= ($activePage??'')==='products'   ? 'active':'' ?>">🛍️ Products</a>
        <a href="categories.php" class="<?= ($activePage??'')==='categories' ? 'active':'' ?>">🏷️ Categories</a>
        <a href="customers.php"  class="<?= ($activePage??'')==='customers'  ? 'active':'' ?>">👥 Customers</a>
        <div class="sidebar-divider"></div>
        <a href="../index.html">🌐 View Site</a>
        <a href="../php/logout.php" class="logout-link">🚪 Logout</a>
    </nav>
    <div class="sidebar-user">👤 <?= htmlspecialchars($_SESSION['name']) ?></div>
</aside>

<main class="admin-main">
