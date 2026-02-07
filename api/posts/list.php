<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$limit  = min((int) ($_GET['limit'] ?? 20), 100);
$offset = (int) ($_GET['offset'] ?? 0);

$posts = FaithGuardRepository::getPosts($limit, $offset);

jsonResponse($posts);
