<?php
// ================================================
//  db_connect.php — Database Connection
//  Umutako Art Store
// ================================================
//  HOW TO USE:
//  1. Open XAMPP / WAMP and start Apache + MySQL
//  2. Change DB_USER and DB_PASS below if needed
//  3. This file is included by all other PHP files
// ================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // ← your MySQL username
define('DB_PASS', '');         // ← your MySQL password (empty by default in XAMPP)
define('DB_NAME', 'umutako_art_store');

// Open connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Stop and show error if connection fails
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Support Kinyarwanda / French special characters
$conn->set_charset('utf8');
?>
