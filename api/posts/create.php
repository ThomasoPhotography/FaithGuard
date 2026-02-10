<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

if (! ENABLE_COMMUNITY_POSTS) {
    errorResponse('Community posts are currently disabled', 403);
}

$user  = requireAuth();
$input = getJsonInput();

// Ensure session and CSRF token exist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

validateRequired($input, ['content', 'csrf_token']);

// CSRF validation
if (! isset($input['csrf_token']) || ! isset($_SESSION['csrf_token']) || ! hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
    errorResponse('Invalid CSRF token', 403);
}

$title       = isset($input['title']) ? sanitize($input['title']) : null;
$content     = sanitize($input['content']);
$isAnonymous = ! empty($input['is_anonymous']);

// Validate lengths
if (strlen($content) < 10) {
    errorResponse('Content must be at least 10 characters', 400);
}
if (strlen($content) > 5000) {
    errorResponse('Content is too long', 400);
}
if ($title !== null && strlen($title) > 200) {
    errorResponse('Title is too long', 400);
}

// Simple rate limiting: prevent multiple posts within 30 seconds
$recent = Database::getSingleRow("SELECT created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC LIMIT 1", [$user['user_id']]);
if ($recent && isset($recent['created_at'])) {
    $last = strtotime($recent['created_at']);
    if ($last !== false && $last > time() - 30) {
        errorResponse('Please wait a moment before creating another post', 429);
    }
}

$result = FaithGuardRepository::createPost([
    'user_id'      => $user['user_id'],
    'title'        => $title,
    'content'      => $content,
    'is_anonymous' => $isAnonymous,
]);

if (! $result) {
    errorResponse('Failed to create post', 500);
}

successResponse();
