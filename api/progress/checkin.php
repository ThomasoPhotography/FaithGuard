<?php
session_start();
require_once '../../database/Database.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
Database::execute("INSERT INTO progress_logs (user_id, checkin_date) VALUES (?, NOW())", [$_SESSION['user_id']]);
echo json_encode(['success' => true]);
?>