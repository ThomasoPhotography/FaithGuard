<?php
session_start();
require_once '../../database/Database.php';
if (!isset($_SESSION['user_id'])) exit(json_encode(['error' => 'Unauthorized']));
$data = json_decode(file_get_contents('php://input'), true);
Database::execute("INSERT INTO posts (user_id, content) VALUES (?, ?)", [$_SESSION['user_id'], $data['content']]);
echo json_encode(['success' => true]);
?>