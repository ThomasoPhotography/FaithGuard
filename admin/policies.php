<?php
require_once __DIR__ . "/../db/database.php";
require_once __DIR__ . "/../db/FaithGuardRepository.php";

session_start();

// Check if admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}
$user_data = FaithGuardRepository::getUserById($_SESSION['user_id']);
if (!$user_data || $user_data['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = $_POST['slug'] ?? '';
    $content_text = $_POST['content_text'] ?? '';

    if (in_array($slug, ['terms', 'privacy', 'cookie']) && !empty($content_text)) {
        // Get existing policy to preserve other fields
        $existing = FaithGuardRepository::getPolicyBySlug($slug);
        if ($existing) {
            // Update content_text (and optionally content_title if needed)
            Database::execute("UPDATE policies SET content_text = ? WHERE slug = ?", [$content_text, $slug]);
            header('Location: profile.php?updated=' . $slug);
            exit;
        } else {
            header('Location: profile.php?error=notfound');
            exit;
        }
    } else {
        header('Location: profile.php?error=invalid');
        exit;
    }
}

// If not POST, redirect to profile
header('Location: profile.php');
exit;
?>