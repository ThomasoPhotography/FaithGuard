<?php
session_start();
require_once '../../database/Database.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit(json_encode(['error' => 'Forbidden']));
$data = json_decode(file_get_contents('php://input'), true);
Database::execute("INSERT INTO resources (title, content) VALUES (?, ?)", [$data['title'], $data['content']]);
echo json_encode(['success' => true]);
?>