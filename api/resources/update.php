<?php
session_start();
require_once __DIR__ . '../../db/database.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Forbidden']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
Database::execute("UPDATE resources SET title = ?, content = ? WHERE id = ?", [$data['title'], $data['content'], $data['id']]);
echo json_encode(['success' => true]);
?>