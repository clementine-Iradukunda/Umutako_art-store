<?php
// get_product.php — Return a single product by ?id=
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['error' => 'Invalid product ID.']);
    exit;
}

$stmt = $conn->prepare(
    "SELECT p.*, c.name AS category_name, c.icon AS category_icon
     FROM products p
     JOIN categories c ON p.category_id = c.id
     WHERE p.id = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row) {
    $host = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $row['image'] = !empty($row['image']) ? $host . '/' . ltrim($row['image'], '/') : null;
}

echo json_encode($row ?: ['error' => 'Product not found.']);
$conn->close();
?>
