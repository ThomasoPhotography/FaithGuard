<?php
session_start();
require_once '../../database/Database.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$score = array_sum($data); // Simple score calc
$tags = $score > 5 ? ['prayer', 'bible-study'] : ['community']; // Example mapping
Database::execute("INSERT INTO quiz_results (user_id, score, tags) VALUES (?, ?, ?)", [$_SESSION['user_id'], $score, json_encode($tags)]);
echo json_encode(['success' => true, 'tags' => $tags]);
?>