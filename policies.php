<?php
    // --- Core App Requirements ---
    require_once $_SERVER['DOCUMENT_ROOT'] . '/db/database.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/db/FaithGuardRepository.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/api/helper/debug.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/api/helper/user.php';

    // --- Session (same pattern as other pages) ---
    session_set_cookie_params([
    'lifetime' => 302400,
    'path'     => '/',
    'domain'   => $_SERVER['SERVER_NAME'] ?? '',
    'secure'   => true,
    'httponly' => true,
    ]);
    session_start();

    // --- Auth State (SAFE DEFAULTS) ---
    $is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

    $user        = false;
    $user_data   = null;
    $accountName = '';
    $user_role   = 'guest';

    // --- Load user if logged in ---
    if ($is_logged_in && isset($_SESSION['user_id'])) {
    $user_data = FaithGuardRepository::getUserById($_SESSION['user_id']);

    if ($user_data) {
        $user        = normalize_user($user_data);
        $accountName = $user['display_name'] ?? ($user['email'] ?? 'User');
        $user_role   = $user['role'] ?? 'user';
    } else {
        // Corrupted session → reset
        unset($_SESSION['user_id'], $_SESSION['logged_in']);
        $is_logged_in = false;
    }
    }

    // --- Policy Logic (PUBLIC ACCESS) ---
    $slug       = $_GET['slug'] ?? '';
    $validSlugs = ['terms', 'privacy', 'cookie'];

    if (! in_array($slug, $validSlugs, true)) {
    $title       = 'Policy Not Found';
    $content     = '<p>The requested policy could not be found.</p>';
    $dateUpdated = 'Unknown';
    $dateCreated = 'Unknown';
    $date        = 'Date Created: ' . $dateCreated . ' - Last Updated: ' . $dateUpdated;
    } else {
    $policy = FaithGuardRepository::getPolicyContent($slug);

    if ($policy) {
        $title       = htmlspecialchars($policy['content_title'] ?? 'Policy');
        $content     = nl2br(htmlspecialchars($policy['content_text'] ?? ''));
        $dateCreated = isset($policy['created_at']) ? htmlspecialchars(date('d M Y', strtotime($policy['created_at']))) : 'Unknown';
        $dateUpdated = isset($policy['updated_at']) ? htmlspecialchars(date('d M Y', strtotime($policy['updated_at']))) : 'Unknown';
        $date        = 'Date Created: ' . $dateCreated . ' - Last Updated: ' . $dateUpdated;
    } else {
        $title       = 'Policy Not Found';
        $content     = '<p>The requested policy could not be found.</p>';
        $dateUpdated = 'Unknown';
        $dateCreated = 'Unknown';
        $date        = 'Date Created: ' . $dateCreated . ' - Last Updated: ' . $dateUpdated;
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title><?php echo $title; ?> - FaithGuard</title>

    <link rel="icon" href="../../assets/uploads/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/main.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light c-nav">
        <div class="container-fluid">
            <!-- LEFT SIDE: LOGO + BRAND -->
            <a class="navbar-brand c-nav__brand" href="index.php">
                <img src="assets/uploads/FaithGuard_Primary_Logo.svg" alt="FaithGuard Logo" class="c-nav__logo">
            </a>
            <button class="navbar-toggler c-nav__toggler c-nav__toggler--btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- LANGUAGE SELECTOR   -->
            <form method="post" action="../../../api/actions/set-language.php" class="d-inline c-nav__language">
                <select name="language" class="form-select form-select-sm c-nav__selector" onchange="this.form.submit()">
                    <option value="en" class="c-nav__selector c-nav__selector--en" <?php echo($_SESSION['language'] ?? 'en') === 'en' ? 'selected' : '' ?>>
                        English
                    </option>
                    <option value="nl" class="c-nav__selector c-nav__selector--nl" <?php echo($_SESSION['language'] ?? '') === 'nl' ? 'selected' : '' ?>>
                        Nederlands
                    </option>
                </select>
            </form>
            <!-- NAV -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Navigation Links (CENTER/LEFT) -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item c-nav__item">
                        <a class="nav-link c-nav__link" href="about.php">About</a>
                    </li>
                    <li class="nav-item c-nav__item">
                        <a class="nav-link c-nav__link" href="resources.php">Resources</a>
                    </li>
                    <li class="nav-item c-nav__item">
                        <a class="nav-link c-nav__link" href="contact.php">Contact</a>
                    </li>
                </ul>
                <!-- RIGHT SIDE: USER/LOGIN DROPDOWN -->
                <?php if ($is_logged_in && $user): ?>
                <!-- Logged-in user menu -->
                <div class="d-flex dropdown c-dropdown">
                    <button class="btn c-btn c-dropdown__btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="c-dropdown__icon bi bi-person-check me-1"></i>
                        <span class="c-dropdown__text">Welcome                                                                                                                                                                                           <?php echo $accountName; ?></span>
                    </button>
                    <!-- LOGGED-IN DROPDOWN MENU -->
                    <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu" aria-labelledby="userDropdown">
                        <li>
                            <h6 class="dropdown-header c-dropdown__header">Signed in as:                                                                                                                                                                                                                                                                         <?php echo ucfirst($user_role); ?></h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <!-- Profile Link (Role-Based) -->
                        <li>
                            <a class="dropdown-item c-dropdown__item" href="<?php echo($user_role === 'admin') ? 'admin/profile.php' : 'users/profile.php'; ?>">
                                <i class="bi bi-person-badge me-2"></i>
                                <span class="c-dropdown__text">Profile / Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item c-dropdown__item js-logout-btn" href="#" onclick="logout()">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                <span class="c-dropdown__text">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <?php else: ?>
                <!-- Guest login/register dropdown -->
                <div class="d-flex dropdown c-dropdown">
                    <button class="btn c-btn c-dropdown__btn js-dropdown-btn dropdown-toggle" type="button" id="loginDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="c-dropdown__icon bi bi-person-circle me-1"></i>
                        <span class="c-dropdown__text">Login / Register</span>
                    </button>
                    <!-- LOGGED-OUT DROPDOWN MENU (Login Form) -->
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
                            <button class="btn c-btn c-dropdown__login js-log mb-2">Login</button>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item c-dropdown__item js-create" href="/register.php">
                                <i class="bi bi-person-plus me-2"></i>
                                <span class="c-dropdown__text">Create Account</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <main class="container my-5">
        <h1 class="c-main__title"><?php echo $title; ?></h1>
        <h3 class="c-main__subtitle"><?php echo $date; ?></h3>
        <div class="c-main__text"><?php echo $content; ?></div>
        <a href="index.php" class="btn c-btn c-btn__dashboard mt-4">Back to Home</a>
    </main>
    <!-- Footer -->
    <footer class="c-footer">
        <div class="container">
            <div class="row">
                <!-- Footer Content: Left -->
                <div class="col-md-6 col-12">
                    <img class="c-footer__logo" src="../../assets/uploads/FaithGuard_Secondary_Logo.svg" alt="Secondary Logo">
                    <p class="c-footer__text">&copy; 2025 FaithGuard. All rights reserved. Overcoming addiction through Christ &amp; Protecting your digital faith with hope and redemption.</p>
                </div>
                <!-- Footer Content: Right -->
                <div class="col-md-6 col-12 text-md-end">
                    <ul class="footer-nav c-footer__nav">
                        <li class="c-footer__item">
                            <a class="c-footer__links" href="index.php">Home</a>
                        </li>
                        <li class="c-footer__item">
                            <a class="c-footer__links" href="resources.html">Resources</a>
                        </li>
                        <li class="c-footer__item">
                            <a class="c-footer__links" href="about.html">About</a>
                    </li>
                    <li class="c-footer__item">
                        <a class="c-footer__links" href="contact.php">Contact</a>
                    </li>
                </ul>
            </div>
            <!-- Footer Content: Policies -->
            <div class="row">
                <div class="col-md-3">
                    <li class="c-footer__item">
                            <a class="c-footer__links" href="policies.php?slug=terms">Terms of Service</a>
                        </li>
                </div>
                <div class="col-md-3">
                    <li class="c-footer__item">
                            <a class="c-footer__links" href="policies.php?slug=privacy">Privacy Policy</a>
                        </li>
                </div>
                <div class="col-md-3">
                    <li class="c-footer__item">
                            <a class="c-footer__links" href="policies.php?slug=cookie">Cookie Policy</a>
                        </li>
                </div>
            </div>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/auth.js"></script>
<script src="../../assets/js/cookie-banner.js"></script>
</body>
</html>
