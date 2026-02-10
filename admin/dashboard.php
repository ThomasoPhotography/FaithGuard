<?php
    require_once __DIR__ . '/../db/database.php';
    require_once __DIR__ . '/../db/FaithGuardRepository.php';

    session_start();
    if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
    }

    $user = FaithGuardRepository::getUserById($_SESSION['user_id']);
    if (! $user || empty($user['is_admin'])) {
    header('Location: /dashboard.php');
    exit;
    }

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>body{padding:20px}</style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($user['first_name'] ?? $user['email']); ?></p>
    <ul>
        <li><a href="/admin/edit-policy.php?type=privacy">Edit Privacy Policy</a></li>
        <li><a href="/admin/edit-policy.php?type=terms">Edit Terms</a></li>
        <li><a href="/admin/edit-policy.php?type=community">Edit Community Guidelines</a></li>
    </ul>
</body>
</html>
