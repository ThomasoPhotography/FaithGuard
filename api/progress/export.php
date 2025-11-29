<?php
session_start();
require_once __DIR__ . '../../db/database.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$data = Database::getRows("SELECT * FROM progress_logs WHERE user_id = ?", [$_SESSION['user_id']]);
$encrypted = openssl_encrypt(json_encode($data), 'aes-256-cbc', 'your-key', 0, 'your-iv'); // Use secure key/IV
echo json_encode(['export' => $encrypted]);
?>