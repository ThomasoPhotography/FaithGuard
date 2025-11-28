<?php
session_start();
require_once __DIR__ . '../../db/database.php';
if (!isset($_SESSION['user_id'])) exit(json_encode(['error' => 'Unauthorized']));
$data = json_decode(file_get_contents('php://input'), true);
Database::execute("DELETE FROM messages WHERE id = ? AND receiver_id = ?", [$data['id'], $_SESSION['user_id']]);
echo json_encode(['success' => true]);
?>