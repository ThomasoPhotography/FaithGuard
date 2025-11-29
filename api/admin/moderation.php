<?php
session_start();
require_once __DIR__ . '../../db/database.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Forbidden']);
    exit;
}
$reports = Database::getRows("SELECT * FROM reports WHERE reviewed = 0");
echo json_encode($reports);
?>