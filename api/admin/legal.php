<?php
session_start();
require_once '/db/database.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Forbidden']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
Database::execute("UPDATE legal_texts SET tos = ?, privacy = ? WHERE id = 1", [$data['tos'], $data['privacy']]);
echo json_encode(['success' => true]);
?>