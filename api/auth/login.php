<?php
// /templates/nav.php
// Start session if not already started (safe check for files included via JS fetch)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure repository is loaded to fetch user details (assuming index.php did not load it)
// Adjust path as necessary from /templates/ to /db/
require_once __DIR__ . '/../../db/FaithGuardRepository.php'; 

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = '';
$user_role = '';

if ($is_logged_in && isset($_SESSION['user_id'])) {
    // Fetch user data to display name/role
    $user_data = FaithGuardRepository::getUserById($_SESSION['user_id']); 
    
    if ($user_data) {
        // Assuming your 'users' table has a 'name' or 'email' column and a 'role' column
        $username = $user_data['name'] ?? $user_data['email']; 
        $user_role = $user_data['role'] ?? 'User';
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">FaithGuard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                </ul>
            
            <div class="d-flex">
                <?php if ($is_logged_in): ?>
                    <span class="navbar-text me-3 text-success">
                        Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong> 
                        (<?php echo htmlspecialchars($user_role); ?>)
                    </span>
                    <?php if ($user_role === 'admin'): ?>
                        <a href="/admin" class="btn btn-sm btn-warning me-2">Admin Panel</a>
                    <?php endif; ?>
                    <button class="btn btn-outline-danger js-logout-btn">Logout</button>
                <?php else: ?>
                    <button class="btn btn-outline-primary js-log">Login / Register</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>