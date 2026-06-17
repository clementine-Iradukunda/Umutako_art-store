<?php
// get_products.php — Return products (with optional category/search filter)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search      = isset($_GET['search'])   ? trim($_GET['search'])   : '';

$base = "SELECT p.*, c.name AS category_name, c.icon AS category_icon
         FROM products p
         JOIN categories c ON p.category_id = c.id";

$conditions = [];
$params     = [];
$types      = '';

if ($category_id > 0) {
    $conditions[] = 'p.category_id = ?';
    $params[]     = $category_id;
    $types       .= 'i';
}
if ($search !== '') {
    $like         = "%$search%";
    $conditions[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[]     = $like;
    $params[]     = $like;
    $types       .= 'ss';
}

$sql = $base;
if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY c.id, p.id';

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result   = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
$conn->close();
?>
