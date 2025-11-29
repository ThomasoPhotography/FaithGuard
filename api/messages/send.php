<?php
session_start();
require_once __DIR__ . '../../db/database.php';
if (!isset($_SESSION['user_id'])) exit(json_encode(['error' => 'Unauthorized']));
$data = json_decode(file_get_contents('php://input'), true);
Database::execute("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)", [$_SESSION['user_id'], $data['receiver_id'], $data['content']]);
echo json_encode(['success' => true]);
?>