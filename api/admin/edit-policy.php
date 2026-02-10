<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

$user = requireAuth();
if (! $user['is_admin']) {
    errorResponse('Admin access required', 403);
}

// Ensure session and CSRF token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$input = getJsonInput();
validateRequired($input, ['slug', 'content_text', 'csrf_token']);

if (! isset($input['csrf_token']) || ! hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
    errorResponse('Invalid CSRF token', 403);
}

$slug  = trim($input['slug']);
$title = trim($input['content_title'] ?? '');
$text  = trim($input['content_text']);

// Basic validation
if ($slug === '') {
    errorResponse('Invalid slug', 400);
}

$existing = FaithGuardRepository::getPolicyContent($slug);
if ($existing) {
    $ok = FaithGuardRepository::updatePolicyContentBySlug($slug, ['content_title' => $title, 'content_text' => $text]);
    if (! $ok) {
        errorResponse('Failed to update policy', 500);
    }
    successResponse(['message' => 'Policy updated']);
} else {
    $created = FaithGuardRepository::createPolicy($slug, $title, $text);
    if (! $created) {
        errorResponse('Failed to create policy', 500);
    }
    successResponse(['message' => 'Policy created']);
}
