<?php
require_once __DIR__ . "/../../db/database.php";
require_once __DIR__ . "/../../db/FaithGuardRepository.php";

session_start();

if (! isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$id   = $_GET['id'] ?? null;
$data = json_decode(file_get_contents('php://input'), true);

if (! $id || ! $data) {
    http_response_code(400);
    exit;
}

FaithGuardRepository::updateResource((int) $id, $data['title'], $data['slug'], $data['content_text'], $data['content_visual'] ?? null, $_SESSION['user_id']);
echo json_encode(['success' => true]);
