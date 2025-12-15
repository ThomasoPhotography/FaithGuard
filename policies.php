<?php
    // --- Core App Requirements (Always required) ---
    require_once __DIR__ . "/db/database.php";
    require_once __DIR__ . "/db/FaithGuardRepository.php";
    // --- Optional Helper/Debug (Required, but note its function) ---
    require_once __DIR__ . "/api/helper/debug.php";

    // Get slug from URL
    $slug       = $_GET['slug'] ?? '';
    $validSlugs = ['terms', 'privacy', 'cookie'];

    if (! in_array($slug, $validSlugs)) {
        $title   = 'Policy Not Found';
        $content = '<p>The requested policy could not be found.</p>';
    } else {
        $policy = FaithGuardRepository::getPolicyContent($slug);
        if ($policy) {
            $title   = htmlspecialchars($policy['content_title']);
            $content = nl2br(htmlspecialchars($policy['content_text']));
        } else {
            $title   = 'Policy Not Found';
            $content = '<p>The requested policy could not be found.</p>';
        }
    }
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
    <meta name="version" content="0.1.4-alpha">
    <meta name="release" content="current">
    <!-- Title -->
    <title><?php echo $title; ?> - FaithGuard</title>
    <!-- Favicon -->
    <link rel="icon" href="assets/uploads/favicon.ico" type="image/x-icon">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/main.css">
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
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Navigation Links (CENTER/LEFT) -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item c-nav__item">
                        <a class="nav-link c-nav__link" href="templates/community.html">Community</a>
                    </li>
                    <li class="nav-item c-nav__item">
                        <a class="nav-link c-nav__link" href="templates/progress.html">Progress</a>
                    </li>
                    <li class="nav-item c-nav__item">
                        <a class="nav-link c-nav__link" href="templates/resources.html">Resources</a>
                    </li>
                </ul>

                <!-- RIGHT SIDE: USER/LOGIN DROPDOWN -->
                <?php if ($is_logged_in && $user): ?>
                <!-- Logged-in user menu -->
                <div class="d-flex dropdown c-dropdown">
                    <button class="btn c-btn c-dropdown__btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="c-dropdown__icon bi bi-person-check me-1"></i>
                        <span class="c-dropdown__text">Welcome                                                                                                                                                                                                                                                                                                                                                                                     <?php echo $accountName; ?></span>                    </button>
                    <!-- LOGGED-IN DROPDOWN MENU -->
                    <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu" aria-labelledby="userDropdown">
                        <li>
                            <h6 class="dropdown-header c-dropdown__header">Signed in as:<?php echo ucfirst($user_role); ?></h6>
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
                            <button class="btn c-btn c-dropdown__login js-log mb-2">Login / Register</button>
                        </li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <main class="c-main container my-5">
        <h1 class="c-main__title"><?php echo $title; ?></h1>
        <div class="c-main__text">
            <?php echo $content; ?>
        </div>
        <a href="index.php" class="btn c-btn c-btn__dashboard mt-3">Back to Home</a>
    </main>
    <!-- Footer -->
    <footer class="c-footer">
        <div class="container">
            <div class="row">
                <!-- Footer Content: Left -->
                <div class="col-md-6 col-12">
                    <img class="c-footer__logo" src="assets/uploads/FaithGuard_Secondary_Logo.svg" alt="Secondary Logo">
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
                            <ul class="footer-nav c-footer__nav c-footer__nav--nested">
                                <li class="c-footer__item">
                                    <a class="c-footer__links" href="policies.php?slug=terms">Terms of Service</a>
                                </li>
                                <li class="c-footer__item">
                                    <a class="c-footer__links" href="policies.php?slug=privacy">Privacy Policy</a>
                                </li>
                                <li class="c-footer__item">
                                    <a class="c-footer__links" href="policies.php?slug=cookie">Cookie Policy</a>
                                </li>
                            </ul>
                        </li>
                        <li class="c-footer__item">
                            <a class="c-footer__links" href="about.html">About</a>
                        </li>
                        <li class="c-footer__item">
                            <a class="c-footer__links" href="contact.php">Contact</a>
                        </li> <!-- Will be added once I upload it to a webhost -->
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</body>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<!-- Custom JS -->
<script src="assets/js/auth.js"></script>
<script src="assets/js/cookie-banner.js"></script>
</html>
