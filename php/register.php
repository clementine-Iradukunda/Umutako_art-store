<?php
header('Content-Type: application/json');
session_start();

require_once 'db_connect.php';

$data    = json_decode(file_get_contents('php://input'), true);
$name    = trim($data['name']     ?? '');
$email   = trim($data['email']    ?? '');
$pass    = trim($data['password'] ?? '');
$phone   = trim($data['phone']    ?? '');
$address = trim($data['address']  ?? '');

if (!$name || !$email || !$pass) {
    echo json_encode(['success' => false, 'message' => 'Name, email and password are required.']);
    exit;
}
if (strlen($pass) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
    exit;
}

// Check duplicate email
$chk = $conn->prepare("SELECT id FROM users WHERE email = ?");
$chk->bind_param('s', $email);
$chk->execute();
if ($chk->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'An account with this email already exists.']);
    exit;
}

$hashed = hash('sha256', $pass);
$stmt   = $conn->prepare("INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, 'customer', ?, ?)");
$stmt->bind_param('sssss', $name, $email, $hashed, $phone, $address);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    exit;
}

$_SESSION['user_id'] = $conn->insert_id;
$_SESSION['name']    = $name;
$_SESSION['email']   = $email;
$_SESSION['role']    = 'customer';

echo json_encode(['success' => true, 'redirect' => 'my-orders.php']);
$conn->close();
?>
