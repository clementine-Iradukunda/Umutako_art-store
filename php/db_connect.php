<?php
// ================================================
//  db_connect.php — Database Connection
//  Umutako Art Store — LIVE SERVER
// ================================================

define('DB_HOST', 'sql212.infinityfree.com');
define('DB_USER', 'if0_42203229');
define('DB_PASS', 'mT23zUHcYUU');
define('DB_NAME', 'if0_42203229_umutako_art_store');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$conn->set_charset('utf8mb4');
?>
