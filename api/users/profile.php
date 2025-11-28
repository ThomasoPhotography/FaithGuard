<?php
session_start();
require_once '/db/database.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user = Database::getSingleRow("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    echo json_encode($user);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    Database::execute("UPDATE users SET name = ? WHERE id = ?", [$data['name'], $_SESSION['user_id']]);
    echo json_encode(['success' => true]);
}
?>