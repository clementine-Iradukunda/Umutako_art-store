<?php
header('Content-Type: application/json');
session_start();

require_once 'db_connect.php';

$data  = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email']    ?? '');
$pass  = trim($data['password'] ?? '');

if (!$email || !$pass) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

$hashed = hash('sha256', $pass);
$stmt   = $conn->prepare("SELECT id, name, email, role FROM users WHERE email = ? AND password = ?");
$stmt->bind_param('ss', $email, $hashed);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['name']    = $user['name'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];

echo json_encode([
    'success'  => true,
    'role'     => $user['role'],
    'name'     => $user['name'],
    'redirect' => $user['role'] === 'admin' ? 'admin/dashboard.php' : 'my-orders.php'
]);
$conn->close();
?>
