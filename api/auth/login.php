<?php
session_start();
require_once __DIR__ . '/../../db/database.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['email'], $data['password'])) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}
$user = Database::getSingleRow("SELECT * FROM users WHERE email = ?", [$data['email']]);
if ($user && password_verify($data['password'], $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['token'] = bin2hex(random_bytes(16));
    echo json_encode(['success' => true, 'token' => $_SESSION['token']]);
} else {
    echo json_encode(['error' => 'Invalid credentials']);
}
?>