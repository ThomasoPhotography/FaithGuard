<?php
require_once '/db/database.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['email'], $data['password'])) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}
$hash = password_hash($data['password'], PASSWORD_DEFAULT);
$result = Database::execute("INSERT INTO users (email, password_hash) VALUES (?, ?)", [$data['email'], $hash]);
echo json_encode(['success' => $result > 0]);
?>