<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$type     = $_GET['type'] ?? null;
$category = $_GET['category'] ?? null;
$limit    = min((int) ($_GET['limit'] ?? 20), 100);

$resources = FaithGuardRepository::getResources($type, $category, $limit);

jsonResponse($resources);
