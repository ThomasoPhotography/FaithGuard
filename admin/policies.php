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
    $content = $_POST['content'] ?? '';

    if (in_array($slug, ['terms', 'privacy', 'cookie']) && !empty($content)) {
        // Update the policy content
        Database::execute("UPDATE policies SET content = ? WHERE slug = ?", [$content, $slug]);
        // Redirect back with success
        header('Location: profile.php?updated=' . $slug);
        exit;
    } else {
        // Redirect back with error
        header('Location: profile.php?error=invalid');
        exit;
    }
}

// If not POST, redirect to profile
header('Location: profile.php');
exit;
?>