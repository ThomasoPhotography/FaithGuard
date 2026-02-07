<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$id = (int) ($_GET['id'] ?? 0);

if (! $id) {
    errorResponse('Resource ID required');
}

$resource = FaithGuardRepository::getResourceById($id);

if (! $resource) {
    errorResponse('Resource not found', 404);
}

// Increment view count
FaithGuardRepository::incrementResourceViews($id);

jsonResponse($resource);
