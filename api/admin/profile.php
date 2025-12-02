<?php
session_set_cookie_params([
    'lifetime' => 86400, // 1 day
    'path' => '/',       // CRITICAL: Make the cookie valid for the whole site
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,    // Recommended for live HTTPS site
    'httponly' => true
]);
session_start();
// Nav bar user variables
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user = null;
$accountName = 'Admin';
$user_role = 'admin';
if ($is_logged_in && isset($_SESSION['user_id'])) {
    // Fetch user data using the repository method
    $user_data = FaithGuardRepository::getUserById($_SESSION['user_id']); 
    
    if ($user_data) {
        $user = true;
        // Assuming your 'users' table has a 'name' or 'email' column and a 'role' column
        $accountName = htmlspecialchars($user_data['name'] ?? $user_data['email']);
        $user_role = $user_data['role'] ?? 'user';
    } else {
        // Logged-in session exists, but user not found in DB (session cleanup needed)
        unset($_SESSION['user_id']);
        unset($_SESSION['logged_in']);
        $is_logged_in = false;
    }
}
// --- Core App Requirements (Always required) ---
require_once __DIR__ . "/db/database.php";
require_once __DIR__ . "/db/FaithGuardRepository.php";
// --- Optional Helper/Debug (Required, but note its function) ---
require_once __DIR__ . "/api/helper/debug.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FaithGuard - Protecting Your Digital Faith">
    <meta name="keywords" content="FaithGuard, Digital Security, Faith Protection, Online Safety">
    <meta name="author" content="WWTW - FaithGuard">
    <meta name="robots" content="noindex">
    <!-- Version -->
    <meta name="version" content="0.1.3-beta">
    <meta name="release" content="2025-11-27">
    <!-- Title -->
    <title>FaithGuard</title>
    <!-- Favicon -->
    <link rel="icon" href="assets/uploads/favicon.ico" type="image/x-icon">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    
</body>
</html>