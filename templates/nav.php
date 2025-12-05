<?php
// /templates/nav.php

// 1. Session Check (CRITICAL: Required when fetched by JS)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Data Access (Fetch user data to make the navbar dynamic)
// Note: This path assumes nav.php is in /templates/, so we go up two levels (../../) to reach /db/
require_once __DIR__ . '/../../db/FaithGuardRepository.php';

$is_logged_in = isset($_SESSION['user_id']);
$user = null;
$accountName = 'Guest';
$user_role = 'user';

if ($is_logged_in && isset($_SESSION['user_id'])) {
    // Fetch the user data from the database
    // We only need id, name (or email), and role to display the status
    $user_data = FaithGuardRepository::getUserById("SELECT id, email, name, role FROM users WHERE id = ?", [$_SESSION['user_id']]);

    if ($user_data) {
        $user = true;
        // Use 'name' if available, otherwise default to email
        $accountName = htmlspecialchars($user_data['name'] ?? $user_data['email']);
        $user_role = $user_data['role'] ?? 'user';
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-light c-nav">
    <div class="container-fluid">
        <a class="navbar-brand c-nav__brand" href="index.php">
            <img src="assets/uploads/FaithGuard_Primary_Logo.svg" alt="FaithGuard Logo" class="c-nav__logo">
            FaithGuard
        </a>
        <button class="navbar-toggler c-nav__toggler c-nav__toggler--btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link c-nav__link" href="templates/community.html">Community</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link c-nav__link" href="templates/progress.html">Progress</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link c-nav__link" href="templates/quiz.html">Quiz</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link c-nav__link" href="templates/resources.html">Resources</a>
                </li>
                <?php if ($user_role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="btn btn-sm btn-warning c-nav__link" href="templates/admin/index.html">Admin Panel</a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <?php if ($is_logged_in && $user): ?>
                <!-- Logged-in user menu -->
                <div class="d-flex me-3 dropdown c-dropdown order-lg-3">
                    <button class="btn c-btn c-dropdown__btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="c-dropdown__icon bi bi-person-check"></i> Welcome <?php echo $accountName; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#" onclick="logout()">Logout</a></li>
                        <!-- Add more user options here, e.g., Profile -->
                    </ul>
                </div>
            <?php else: ?>
                <!-- Guest login/register dropdown -->
                <div class="d-flex me-3 dropdown c-dropdown order-lg-3">
                    <button class="btn c-btn c-dropdown__btn js-dropdown-btn dropdown-toggle" type="button" id="loginDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="c-dropdown__icon bi bi-person-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu js-dropdown-menu" aria-labelledby="loginDropdown">
                        <li>
                            <h6 class="dropdown-header c-dropdown__header">Sign Up / Log In</h6>
                        </li>
                        <li>
                            <input type="email" id="signupUsername" class="form-control c-dropdown__info mb-2" placeholder="Email">
                        </li>
                        <li>
                            <input type="password" id="signupPassword" class="form-control c-dropdown__info mb-2" placeholder="Password">
                        </li>
                        <li>
                            <button class="btn c-btn c-dropdown__btn js-log btn-sm mb-2">Sign Up / Log In</button>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>