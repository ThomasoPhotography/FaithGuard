<?php
session_start();
require_once __DIR__ . '../../db/database.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Forbidden']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
Database::execute("DELETE FROM resources WHERE id = ?", [$data['id']]);
echo json_encode(['success' => true]);
?>