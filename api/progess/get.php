<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$user = requireAuth();

$progress = FaithGuardRepository::getUserProgress($user['user_id']);
$checkins = FaithGuardRepository::getUserCheckins($user['user_id'], 30);

successResponse([
    'progress' => $progress,
    'checkins' => $checkins,
]);
