<?php
    // --- Core App Requirements (Always required) ---
    require_once __DIR__ . "/../db/database.php";
    require_once __DIR__ . "/../db/FaithGuardRepository.php";
    require_once __DIR__ . "/../api/helper/debug.php";

    session_set_cookie_params([
        'lifetime' => 302400, // 3.5 days (84 hours)
        'path'     => '/',
        'domain'   => $_SERVER['SERVER_NAME'] ?? '',
        'secure'   => true,
        'httponly' => true,
    ]);
    session_start();

    // --- INITIALIZE VARIABLES ---
    $is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

    // --- CRITICAL FIX: Define user variables needed for navigation bar ---
    $user        = null;
    $accountName = '';
    $user_role   = 'admin';
    $user_data   = null;
    $userId      = $_SESSION['user_id'] ?? null;
    $user_link   = '';

    if ($is_logged_in && isset($_SESSION['user_id'])) {
        // Fetch user data using the repository method
        $user_data = FaithGuardRepository::getUserById($_SESSION['user_id']);

        if ($user_data) {
            $user = true;
            // Assuming your 'users' table has a 'name' or 'email' column and a 'role' column
            $accountName = htmlspecialchars($user_data['name'] ?? $user_data['email']);
            $user_role   = $user_data['role'] ?? 'admin';
            $user_link   = ($user_role === 'user') ? '../users/profile.php' : 'profile.php';
        } else {
            // Logged-in session exists, but user not found in DB (session cleanup needed)
            unset($_SESSION['user_id']);
            unset($_SESSION['logged_in']);
            $is_logged_in = false;
            header("Location: ../api/auth/register.php");
            exit();
        }
    }

    // --- Fetch Dynamic Data ---
    // --- Flagged Reports ---
    $reports = FaithGuardRepository::getAllReportedPosts();

    // --- Resource Count ---
    $allResources  = FaithGuardRepository::getAllResources();
    $resourceCount = count($allResources);

    // --- Legal texts ---
    $tosPolicy = FaithGuardRepository::getPolicyContent('terms');
    if (! $tosPolicy) {
        $tosTitle = 'Terms of Service';
        $tosText  = 'Policy content not available.';
    } else {
        $tosTitle = $tosPolicy ? $tosPolicy['content_title'] : 'Terms of Service';
        $tosText  = $tosPolicy ? $tosPolicy['content_text'] : 'Policy content not available.';
    }
    $privacyPolicy = FaithGuardRepository::getPolicyContent('privacy');
    if (! $privacyPolicy) {
        $privacyTitle = 'Privacy Policy';
        $privacyText  = 'Policy content not available.';
    } else {
        $privacyTitle = $privacyPolicy ? $privacyPolicy['content_title'] : 'Privacy Policy';
        $privacyText  = $privacyPolicy ? $privacyPolicy['content_text'] : 'Policy content not available.';
    }
    $cookiePolicy = FaithGuardRepository::getPolicyContent('cookie');
    if (! $cookiePolicy) {
        $cookieTitle = 'Cookie Policy';
        $cookieText  = 'Policy content not available.';
    } else {
        $cookieTitle = $cookiePolicy ? $cookiePolicy['content_title'] : 'Cookie Policy';
        $cookieText  = $cookiePolicy ? $cookiePolicy['content_text'] : 'Policy content not available.';
    }
    // --- Recent messages ---
    if (isset($_SESSION['user_id'])) {
        $recentMessages = FaithGuardRepository::getMessagesByUserId($_SESSION['user_id']);
        $recentMessages = array_slice($recentMessages, 0, 5);
    }

    // Nav bar variables
    $memberSince = date('d/M/Y', strtotime($user_data['created_at']));
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
    <title>FaithGuard - Admin</title>
    <!-- Favicon -->
    <link rel="icon" href="../assets/uploads/favicon.ico" type="image/x-icon">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light c-nav">
        <div class="container-fluid">
            <!-- LEFT SIDE: LOGO + BRAND -->
            <a class="navbar-brand c-nav__brand" href="../index.php">
                <img src="../assets/uploads/FaithGuard_Primary_Logo.svg" alt="FaithGuard Logo" class="c-nav__logo">
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
                        <span class="c-dropdown__text">Welcome                                                                                                                             <?php echo $accountName; ?></span>
                    </button>
                    <!-- LOGGED-IN DROPDOWN MENU -->
                    <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu" aria-labelledby="userDropdown">
                        <li>
                            <h6 class="dropdown-header c-dropdown__header">Signed in as:                                                                                                                                                                                 <?php echo ucfirst($user_role); ?></h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <!-- Profile Link (Role-Based) -->
                        <li>
                            <a class="dropdown-item c-dropdown__item" href="<?php echo($user_role === 'admin') ? 'profile.php' : 'users/profile.php'; ?>">
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
                            <a class="dropdown-item c-dropdown__item js-create" href="../api/auth/register.php">
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
    <!-- Main -->
    <main class="c-main container my-5">
        <section class="c-dashboard c-admin__profile">
            <!-- ROW 1 : ACCOUNT SUMMARY -->
            <div class="c-dashboard__item c-slot-1-1 c-span-6">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Account Summary</h5>
                        <p class="mb-1"><strong>Name:</strong>                                                               <?php echo $accountName; ?></p>
                        <p class="mb-1"><strong>Role:</strong>                                                               <?php echo ucfirst($user_role); ?></p>
                        <p class="mb-0"><strong>Member Since:</strong>                                                                       <?php echo $memberSince; ?></p>
                    </div>
                </div>
            </div>
            <!-- ROW 1 : LATEST ASSESSEMENT -->
            <div class="c-dashboard__item c-slot-7-1 c-span-6">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-journal-check me-2"></i> Latest Assessment</h5>
                        <?php if (is_array($latestQuizResult)): ?>
                        <?php
                            $score     = (int) ($latestQuizResult['total_score'] ?? 0);
                            $date      = ! empty($latestQuizResult['created_at']) ? date('d M, Y', strtotime($latestQuizResult['created_at'])) : '—';
                            $types     = json_decode($latestQuizResult['addiction_type'] ?? '[]', true);
                            $typesText = is_array($types) ? implode(', ', $types) : '';
                        ?>
                        <p><strong>Date:</strong><?php echo $date ?></p>
                        <p><strong>Score:</strong>
                            <span class="badge bg-primary"><?php echo $score ?>%</span>
                        </p>
                        <p><strong>Focus Areas:</strong>
                            <?php echo htmlspecialchars($typesText, ENT_QUOTES) ?>
                        </p>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="/quiz.php" class="btn c-btn w-100">Retake Assessment</a>
                            </div>
                            <div class="col-6">
                                <a href="/resources.php" class="btn c-btn w-100">View Resources</a>
                            </div>
                        </div>
                        <?php else: ?>
                            <p>You haven’t completed an assessment yet.</p>
                            <a href="/quiz.php" class="btn c-btn w-100">Take the Assessment</a>
                        <?php endif; ?>
                    </div>
                    <button class="btn c-btn c-btn__outline js-open-pastoral-modal" data-endpoint="/api/modals/pastoral-summary.php">
                        <i class="bi bi-journal-heart me-2"></i> View Pastoral Insight
                    </button>
                </div>
            </div>
            <!-- ROWS 2–3 : PRIVACY POLICY -->
            <div class="c-dashboard__item c-slot-1-2 c-span-3 c-row-span-2">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Privacy Policy</h5>
                        <form action="policies.php" method="POST">
                            <input type="hidden" name="slug" value="privacy">
                            <input type="text" name="content_title" class="form-control mb-2" value="<?php echo htmlspecialchars($privacyTitle); ?>">
                            <textarea name="content_text" class="form-control mb-3" rows="6"><?php echo htmlspecialchars($privacyText); ?></textarea>
                            <button class="btn c-btn c-btn__dashboard w-100">Update Privacy Policy</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- ROWS 2–3 : TERMS OF SERVICE -->
            <div class="c-dashboard__item c-slot-4-2 c-span-3 c-row-span-2">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Terms of Service</h5>
                        <form action="policies.php" method="POST">
                            <input type="hidden" name="slug" value="terms">
                            <input type="text" name="content_title" class="form-control mb-2" value="<?php echo htmlspecialchars($tosTitle); ?>">
                            <textarea name="content_text" class="form-control mb-3" rows="6"><?php echo htmlspecialchars($tosText); ?></textarea>
                            <button class="btn c-btn c-btn__dashboard w-100">Update Terms</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- ROWS 2–3 : COOKIE POLICY -->
            <div class="c-dashboard__item c-slot-7-2 c-span-6 c-row-span-2">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Cookie Policy</h5>
                        <form action="policies.php" method="POST">
                            <input type="hidden" name="slug" value="cookie">
                            <input type="text" name="content_title" class="form-control mb-2" value="<?php echo htmlspecialchars($cookieTitle); ?>">
                            <textarea name="content_text" class="form-control mb-3" rows="6"><?php echo htmlspecialchars($cookieText); ?></textarea>
                            <button class="btn c-btn c-btn__dashboard w-100">Update Cookie Policy</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- ROWS 4–5 : REPORTS -->
            <div class="c-dashboard__item c-slot-1-4 c-span-3 c-row-span-2">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Reports</h5>
                        <p class="small text-muted mb-2"><?php echo count($reports); ?> pending</p>
                        <a href="/admin/moderation.php" class="btn c-btn c-btn__dashboard w-100">View Reports</a>
                    </div>
                </div>
            </div>
            <!-- ROWS 4–5 : RESOURCES LIST -->
            <div class="c-dashboard__item c-slot-4-4 c-span-3 c-row-span-2">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Resources</h5>
                        <p><strong>Total:</strong>                                                   <?php echo $resourceCount; ?></p>
                        <a href="../resources/list.php" class="btn c-btn c-btn__dashboard w-100">Manage Resources</a>
                    </div>
                </div>
            </div>
            <!-- ROWS 4–5 : NEW RESOURCE MAKER -->
            <div class="c-dashboard__item c-slot-7-4 c-span-3 c-row-span-2">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Create Resource</h5>
                        <form action="../api/resources/create.php" method="POST">
                            <input type="text" name="title" class="form-control mb-2" placeholder="Title" required>
                            <input type="url" name="video" class="form-control mb-2" placeholder="Video link">
                            <textarea name="content" class="form-control mb-3" rows="4" placeholder="Content" required></textarea>
                            <button class="btn c-btn c-btn__create w-100">Create new Resource</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- ROWS 4–5 : MESSAGES -->
            <div class="c-dashboard__item c-slot-10-4 c-span-3 c-row-span-2">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Messages</h5>
                        <p class="text-muted small">Recent administrative messages.</p>
                        <!-- Placeholder for future message list -->
                    </div>
                </div>
            </div>
            <!-- ROWS 6–9 : FEZ -->
            <div class="c-dashboard__item c-slot-1-6 c-span-12 c-row-span-4">
                <div class="card c-profile__card h-100 c-feature--locked">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="text-center">
                        <i class="bi bi-lock-fill"></i>
                        <p class="mb-0">Future Expansion Zone</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer -->
    <footer class="c-footer">
        <div class="container">
            <div class="row">
                <!-- Footer Content: Left -->
                <div class="col-md-6 col-12">
                    <img class="c-footer__logo" src="../assets/uploads/FaithGuard_Secondary_Logo.svg" alt="Secondary Logo">
                    <p class="c-footer__text">&copy; 2025 FaithGuard. All rights reserved. Overcoming addiction through Christ &amp; Protecting your digital faith with hope and redemption.</p>
                </div>
                <!-- Footer Content: Right -->
                <div class="col-md-6 col-12 text-md-end">
                    <ul class="footer-nav c-footer__nav">
                        <li class="c-footer__item">
                            <a class="c-footer__links" href="../index.php">Home</a>
                        </li>
                        <li class="c-footer__item">
                            <a class="c-footer__links" href="../resources.html">Resources</a>
                        </li>
                        <li class="c-footer__item">
                            <a class="c-footer__links" href="../about.html">About</a>
                    </li>
                    <li class="c-footer__item">
                        <a class="c-footer__links" href="../contact.php">Contact</a>
                    </li>
                </ul>
            </div>
            <!-- Footer Content: Policies -->
            <div class="row">
                <div class="col-md-3">
                    <li class="c-footer__item">
                            <a class="c-footer__links" href="../policies.php?slug=terms">Terms of Service</a>
                        </li>
                </div>
                <div class="col-md-3">
                    <li class="c-footer__item">
                            <a class="c-footer__links" href="../policies.php?slug=privacy">Privacy Policy</a>
                        </li>
                </div>
                <div class="col-md-3">
                    <li class="c-footer__item">
                            <a class="c-footer__links" href="../policies.php?slug=cookie">Cookie Policy</a>
                        </li>
                </div>
            </div>
        </div>
    </footer>
</body>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<!-- Custom JS -->
<script src="../assets/js/auth.js"></script>
<script src="../assets/js/cookie-banner.js"></script>
<script src="../assets/js/timeline-modal.js"></script>
<script src="../assets/js/profile-pastoral-modal.js"></script>
</html>
