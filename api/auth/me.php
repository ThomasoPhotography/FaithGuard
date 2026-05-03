<?php
require_once __DIR__ . '/../base.php';
require_once __DIR__ . '/../helper/user.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

$user = getCurrentUser();

if (! $user) {
    jsonResponse(['success' => false, 'authenticated' => false]);
}

// Get full user data
$fullUser    = FaithGuardRepository::getUserById($user['user_id']);
$preferences = FaithGuardRepository::getUserPreferences($user['user_id']);
$progress    = FaithGuardRepository::getUserProgress($user['user_id']);

// Normalize user for consumer convenience (adds display_name and role)
$normalized = normalize_user($fullUser);

successResponse([
    'authenticated' => true,
    'user'          => [
        'id'           => $fullUser['id'],
        'email'        => $fullUser['email'],
        'first_name'   => $fullUser['first_name'],
        'last_name'    => $fullUser['last_name'],
        'display_name' => $normalized['display_name'] ?? ($fullUser['first_name'] ?? $fullUser['email']),
        'role'         => $normalized['role'] ?? ($fullUser['is_admin'] ? 'admin' : 'user'),
        'avatar_url'   => $fullUser['avatar_url'],
        'bio'          => $fullUser['bio'],
        'is_admin'     => (bool) $fullUser['is_admin'],
        'created_at'   => $fullUser['created_at'],
        'preferences'  => $preferences,
        'progress'     => $progress,
    ],
]);
