<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function isLoggedIn()  { return isset($_SESSION['user_id']); }
function isAdmin()     { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }
function isCustomer()  { return isset($_SESSION['role']) && $_SESSION['role'] === 'customer'; }

function requireLogin($redirect = '../login.html') {
    if (!isLoggedIn()) { header("Location: $redirect"); exit; }
}
function requireAdmin($redirect = '../login.html') {
    if (!isAdmin()) { header("Location: $redirect"); exit; }
}
function requireCustomer($redirect = '../login.html') {
    if (!isCustomer() && !isAdmin()) { header("Location: $redirect"); exit; }
}
?>
