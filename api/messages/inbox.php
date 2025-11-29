<?php
session_start();
require_once __DIR__ . '../../db/database.php';
if (!isset($_SESSION['user_id'])) exit(json_encode(['error' => 'Unauthorized']));
$messages = Database::getRows("SELECT * FROM messages WHERE receiver_id = ?", [$_SESSION['user_id']]);
echo json_encode($messages);
?>