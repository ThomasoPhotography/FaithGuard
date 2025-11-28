<?php
session_start();
require_once '../../db/database.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$user = Database::getSingleRow("SELECT * FROM users WHERE email = ?", [$data['email']]);
if ($user && password_verify($data['password'], $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['token'] = bin2hex(random_bytes(16));
    echo json_encode(['success' => true, 'token' => $_SESSION['token']]);
} else {
    echo json_encode(['error' => 'Invalid credentials']);
}
?>