<?php
session_start();
require_once __DIR__ . '../../db/database.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$progress = Database::getRows("SELECT * FROM progress_logs WHERE user_id = ?", [$_SESSION['user_id']]);
echo json_encode($progress);
?>