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

validateRequired($input, ['content']);

$title       = isset($input['title']) ? sanitize($input['title']) : null;
$content     = sanitize($input['content']);
$isAnonymous = $input['is_anonymous'] ?? false;

if (strlen($content) < 10) {
    errorResponse('Content must be at least 10 characters');
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
