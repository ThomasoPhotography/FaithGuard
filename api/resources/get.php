<?php
require_once __DIR__ . "/../../db/database.php";
require_once __DIR__ . "/../../db/FaithGuardRepository.php";

header('Content-Type: application/json');

$slug = $_GET['slug'] ?? null;

if (! $slug) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing resource slug']);
    exit;
}

$resource = FaithGuardRepository::getResourceBySlug($slug);

if (! $resource) {
    http_response_code(404);
    echo json_encode(['error' => 'Resource not found']);
    exit;
}

echo json_encode($resource);
