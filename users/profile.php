<?php
/* =========================================================
   CORE REQUIREMENTS
========================================================= */
require_once __DIR__ . "/../db/database.php";
require_once __DIR__ . "/../db/FaithGuardRepository.php";
require_once __DIR__ . "/../api/helper/debug.php";

/* =========================================================
   SESSION SETUP
========================================================= */
session_set_cookie_params([
    'lifetime' => 302400,
    'path'     => '/',
    'domain'   => $_SERVER['SERVER_NAME'] ?? '',
    'secure'   => true,
    'httponly' => true,
]);
session_start();

/* =========================================================
   AUTH GUARD (USER ONLY)
========================================================= */
if (
    !isset($_SESSION['logged_in'], $_SESSION['user_id']) ||
    $_SESSION['logged_in'] !== true
) {
    header('Location: ../index.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$user   = FaithGuardRepository::getUserById($userId);

if (!$user) {
    session_destroy();
    header('Location: ../index.php');
    exit;
}

$user_role = $user['role'] ?? 'user';
if ($user_role !== 'user') {
    http_response_code(403);
    exit('Access denied');
}

/* =========================================================
   USER CONTEXT
========================================================= */
$accountName = htmlspecialchars($user['name'] ?? $user['email'], ENT_QUOTES);
$memberSince = date('d M, Y', strtotime($user['created_at']));

/* =========================================================
   DASHBOARD DATA
========================================================= */

// Progress
$progressLogs   = FaithGuardRepository::getProgressLogsByUserId($userId);
$recentCheckins = array_slice($progressLogs, 0, 5);
$totalCheckins  = count($progressLogs);

// Quiz (latest only)
$quizResults        = FaithGuardRepository::getQuizResultsByUserId($userId);
$latestQuizResult   = $quizResults[0] ?? null;

// Inbox
$inboxMessages = FaithGuardRepository::getInboxByUserId($userId);
$recentInbox   = array_slice($inboxMessages, 0, 5);

// Stats
$posts       = FaithGuardRepository::getPostsByUserId($userId);
$totalPosts  = count($posts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard – FaithGuard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="icon" href="../assets/uploads/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light c-nav">
        <div class="container-fluid">
            <!-- LEFT SIDE: LOGO + BRAND -->
            <a class="navbar-brand c-nav__brand" href="../index.php">
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
                        <span class="c-dropdown__text">Welcome                                                                                                                                                                                                                                                         <?php echo $accountName; ?></span>
                    </button>
                    <!-- LOGGED-IN DROPDOWN MENU -->
                    <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu" aria-labelledby="userDropdown">
                        <li>
                            <h6 class="dropdown-header c-dropdown__header">Signed in as:                                                                                                                                                                                                                                                                                                                                                                 <?php echo ucfirst($user_role); ?></h6>
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
                            <a class="dropdown-item c-dropdown__item js-create" href="/api/auth/register.php">
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
    <main class="container my-5 c-main">
        <section class="c-user__profile">
            <!-- Section: Account Summary -->
            <div class="c-user__item">
                <div class="card c-profile__card">
                    <div class="card-body">
                        <h5><i class="bi bi-person-circle me-2"></i>Account Summary</h5>
                        <ul class="list-group list-group-flush mt-3">
                            <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
                            <li class="list-group-item"><strong>Member Since:</strong> <?php echo $memberSince; ?></li>
                            <li class="list-group-item"><strong>Total Posts:</strong> <?php echo $totalPosts; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Section: Progress -->
            <div class="c-user__item">
                <div class="card c-profile__card">
                    <div class="card-body">
                        <h5><i class="bi bi-clipboard-check me-2"></i> Accountability</h5>
                        <p>You have <strong><?php echo $totalCheckins; ?></strong> check-ins.</p>
                        <ul class="list-group list-group-flush">
                            <?php if ($recentCheckins): ?>
                                <?php foreach ($recentCheckins as $log): ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><?php echo date('d/M/Y', strtotime($log['checkin_date'])); ?></span>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($log['milestone'] ?? 'Check-in'); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No check-ins yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Section: Latest Assessment -->
            <div class="c-user__item">
                <div class="card c-profile__card">
                    <div class="card-body">
                        <h5><i class="bi bi-journal-check me-2"></i> Latest Assessment</h5>
                        <?php if ($latestQuizResult): ?>
                            <p><strong>Date:</strong> <?php echo date('d/M/Y', strtotime($latestQuizResult['created_at'])); ?></p>
                            <p><strong>Score:</strong> <?php echo (int) $latestQuizResult['total_score']; ?>%</p>
                            <p><strong>Focus Areas:</strong>
                                <?php $types = json_decode($latestQuizResult['addiction_type'], true); echo htmlspecialchars(implode(', ', $types));?>
                            </p>
                            <a href="../templates/resources.html" class="btn c-btn c-btn__dashboard">View Resources</a>
                            <?php else: ?>
                                <p>No assessment taken yet.</p>
                                <a href="../quiz.php" class="btn c-btn c-btn__dashboard">Take Assessment</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Section: Messages -->
            <div class="c-user__item">
                <div class="card c-profile__card">
                    <div class="card-body">
                        <h5><i class="bi bi-envelope-open me-2"></i> Messages</h5>
                        <ul class="list-group list-group-flush">
                            <?php if ($recentInbox): ?>
                                <?php foreach ($recentInbox as $msg): ?>
                                    <li class="list-group-item">
                                        <strong>From:</strong> User <?php echo (int) $msg['sender_id']; ?><br>
                                        <small><?php echo htmlspecialchars($msg['content']); ?></small>
                                    </li>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <li class="list-group-item">Inbox empty.</li>
                            <?php endif; ?>
                        </ul>
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
<script src="../assets/js/auth.js"></script>
</body>
</html>
