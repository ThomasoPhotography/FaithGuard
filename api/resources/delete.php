<?php
require_once __DIR__ . "/../../db/database.php";
require_once __DIR__ . "/../../db/FaithGuardRepository.php";

session_start();

if (! isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$id = $_GET['id'] ?? null;

if (! $id) {
    http_response_code(400);
    exit;
}

FaithGuardRepository::deleteResource((int) $id);
echo json_encode(['success' => true]);
