<?php
// place_order.php — Save order + customer to database
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed.']);
    exit;
}

require_once 'db_connect.php';

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (empty($data['customer_name']) || empty($data['customer_email']) ||
    empty($data['customer_address']) || empty($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

$name    = trim($data['customer_name']);
$email   = trim($data['customer_email']);
$phone   = trim($data['customer_phone']   ?? '');
$address = trim($data['customer_address']);
$notes   = trim($data['notes']            ?? '');
$total   = (float) ($data['total']        ?? 0);
$cart    = $data['cart'];

// Upsert customer (insert or update phone/address if email already exists)
$stmt = $conn->prepare(
    "INSERT INTO customers (name, email, phone, address)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE name = VALUES(name), phone = VALUES(phone), address = VALUES(address)"
);
$stmt->bind_param('ssss', $name, $email, $phone, $address);
$stmt->execute();
$stmt->close();

// Insert order
$stmt = $conn->prepare(
    "INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, total_amount, notes)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param('ssssds', $name, $email, $phone, $address, $total, $notes);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Could not save order.']);
    exit;
}

$order_id = $conn->insert_id;
$stmt->close();

// Insert order items
$stmt = $conn->prepare(
    "INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price)
     VALUES (?, ?, ?, ?, ?)"
);
foreach ($cart as $item) {
    $pid        = (int)   $item['id'];
    $pname      = (string)$item['name'];
    $qty        = (int)   $item['quantity'];
    $unit_price = (float) $item['price'];
    $stmt->bind_param('iisid', $order_id, $pid, $pname, $qty, $unit_price);
    $stmt->execute();
}
$stmt->close();

echo json_encode(['success' => true, 'message' => 'Order placed successfully!', 'order_id' => $order_id]);
$conn->close();
?>
