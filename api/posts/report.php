<?php
require_once __DIR__ . '/../../db/database.php';
require_once __DIR__ . '/../../db/FaithGuardRepository.php';
require_once __DIR__ . '/../helper/debug.php';
header('Content-Type: application/json');
// Start session
session_start();
// Check if user is logged in
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
if (! $is_logged_in) {
    echo json_encode(['error' => 'You must be logged in to report a post.']);
    exit;
}
$user_id = $_SESSION['user_id'];
// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
    $reason  = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING) ?? 'No reason provided';
    // Validate inputs
    if (! $post_id) {
        echo json_encode(['error' => 'Invalid or missing post ID.']);
        exit;
    }
    // Check if post exists
    $post = FaithGuardRepository::getPostById($post_id);
    if (! $post) {
        echo json_encode(['error' => 'Post not found.']);
        exit;
    }
    // Check if user has already reported this post (prevent duplicates)
    // Assuming a 'reports' table with post_id and user_id
    $existing_report = Database::getSingleRow(
        "SELECT id FROM reports WHERE post_id = ? AND user_id = ?",
        [$post_id, $user_id]
    );
    if ($existing_report) {
        echo json_encode(['error' => 'You have already reported this post.']);
        exit;
    }
    // Insert report into database
    // If FaithGuardRepository has a createReport method, use it; otherwise, use direct SQL
    $report_created = Database::execute(
        "INSERT INTO reports (post_id, user_id, reason, created_at) VALUES (?, ?, ?, NOW())",
        [$post_id, $user_id, $reason]
    );
    if ($report_created) {
        echo json_encode(['success' => true, 'message' => 'Post reported successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to report the post. Please try again.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>