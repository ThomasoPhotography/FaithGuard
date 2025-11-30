<?php
session_start();
require_once __DIR__ . '/../db/FaithGuardRepository.php';  // Include repository for DB access

// If logged in, get user data
$user = null;
$accountName = '';
if (isset($_SESSION['user_id'])) {
    $user = FaithGuardRepository::getUserById($_SESSION['user_id']);
    if ($user) {
        // Extract part before '@' from email
        $emailParts = explode('@', $user['email']);
        $accountName = htmlspecialchars($emailParts[0]);  // Sanitize for security
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
            </ul>
            <?php if (isset($_SESSION['user_id']) && $user): ?>
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