<?php
    declare (strict_types = 1);

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
    $is_logged_in = isset($_SESSION['logged_in'], $_SESSION['user_id'])
        && $_SESSION['logged_in'] === true;

    if (! $is_logged_in) {
        header("Location: ../api/auth/login.php");
        exit;
    }

    $userId = (int) $_SESSION['user_id'];
    $user   = FaithGuardRepository::getUserById($userId);

    if (! is_array($user)) {
        session_destroy();
        header("Location: ../api/auth/login.php");
        exit;
    }

    /* =========================================================
   USER CONTEXT
========================================================= */
    $accountName = htmlspecialchars(
        $user['name'] ?? $user['email'] ?? 'User',
        ENT_QUOTES,
        'UTF-8'
    );

    $user_role = $user['role'] ?? 'user';
    $user_link = ($user_role === 'admin')
        ? '../admin/profile.php'
        : 'profile.php';

    $memberSince = ! empty($user['created_at'])
        ? date('d M, Y', strtotime($user['created_at']))
        : '—';

    /* =========================================================
   DASHBOARD DATA
========================================================= */

    // Progress
    $progressLogs   = FaithGuardRepository::getProgressLogsByUserId($userId) ?? [];
    $recentCheckins = array_slice($progressLogs, 0, 5);
    $totalCheckins  = count($progressLogs);

    // Quiz
    $quizResults      = FaithGuardRepository::getQuizResultsByUserId($userId) ?? [];
    $latestQuizResult = $quizResults[0] ?? null;

    // Inbox
    $inboxMessages = FaithGuardRepository::getInboxByUserId($userId) ?? [];
    $recentInbox   = array_slice($inboxMessages, 0, 5);

    // Posts
    $posts      = FaithGuardRepository::getPostsByUserId($userId) ?? [];
    $totalPosts = count($posts);
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
    <title>FaithGuard</title>
    <!-- Favicon -->
    <link rel="icon" href="../assets/uploads/favicon.ico" type="image/x-icon">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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
                        <span class="c-dropdown__text">Welcome                                                                                                                                                                                                                                                                                                                                                                                     <?php echo $accountName; ?></span>
                    </button>
                    <!-- LOGGED-IN DROPDOWN MENU -->
                    <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu" aria-labelledby="userDropdown">
                        <li>
                            <h6 class="dropdown-header c-dropdown__header">Signed in as:                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <?php echo ucfirst($user_role); ?></h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <!-- Profile Link (Role-Based) -->
                        <li>
                            <a class="dropdown-item c-dropdown__item" href="<?php echo($user_role === 'user') ? 'profile.php' : '/admin/profile.php'; ?>">
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
            <div class="c-user__item c-user__item--1">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-person-circle me-2"></i>Account Summary</h5>
                        <ul class="list-group list-group-flush mt-3">
                            <li class="list-group-item"><strong>Email:</strong>                                                                                                                                                                                                                                                                                                                             <?php echo htmlspecialchars($user['email']); ?></li>
                            <li class="list-group-item"><strong>Member Since:</strong>                                                                                                                                                                                                                                                                                                                                                         <?php echo $memberSince; ?></li>
                            <li class="list-group-item"><strong>Total Interactions:</strong>                                                                                                                                                                                                                                                                                                                                                                                 <?php echo $totalPosts; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Section: Accountability / Progress -->
            <div class="c-user__item c-user__item--2">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-clipboard-check me-2"></i> Progress </h5>
                        <p>You have <strong><?php echo $totalCheckins; ?></strong> total check-ins.</p>
                        <ul class="list-group list-group-flush">
                            <?php if ($recentCheckins): ?>
                                <?php foreach ($recentCheckins as $log): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?php echo date('d M, Y', strtotime($log['checkin_date'])); ?></span>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($log['milestone'] ?? 'Check-in'); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No check-ins logged recently.</li>
                            <?php endif; ?>
                        </ul>
                        <a href="/api/progress/checkin.php" class="btn c-btn c-btn__dashboard w-100">Log a Check-in</a>
                    </div>
                </div>
            </div>
            <!-- Section: Latest Assessment -->
            <div class="c-user__item c-user__item--3">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-journal-check me-2"></i> Latest Assessment</h5>
                        <?php if ($latestQuizResult = "100%"): ?>
                            <div class="mt-3">
                                <p class="mb-1"><strong>Date:</strong>                                                                                                                                                                                                                   <?php echo date('d M, Y', strtotime($latestQuizResult['created_at'])); ?></p>
                                <p class="mb-1"><strong>Score:</strong> <span class="badge bg-primary"><?php echo (int) $latestQuizResult['total_score']; ?>%</span></p>
                                <p class="mb-3"><strong>Primary Focus:</strong>
                                    <span class="text-muted">
                                        <?php $types = json_decode($latestQuizResult['addiction_type'], true);
                                        echo is_array($types) ? htmlspecialchars(implode(', ', $types)) : htmlspecialchars((string) $latestQuizResult['addiction_type']); ?>
                                    </span>
                                </p>
                                <a href="/resources.php" class="btn c-btn c-btn__dashboard w-100">View Recommended Resources</a>
                            </div>
                        <?php elseif ($latestQuizResult > "100%"): ?>
                            <div class="mt-3">
                                <p class="mb-1"><strong>Date:</strong>                                                                                                                                                                                                                   <?php echo date('d M, Y', strtotime($latestQuizResult['created_at'])); ?></p>
                                <p class="mb-1"><strong>Score:</strong> <span class="badge bg-primary"><?php echo (int) $latestQuizResult['total_score']; ?>%</span></p>
                                <p class="mb-3"><strong>Primary Focus:</strong>
                                    <span class="text-muted">
                                        <?php $types = json_decode($latestQuizResult['addiction_type'], true);
                                        echo is_array($types) ? htmlspecialchars(implode(', ', $types)) : htmlspecialchars((string) $latestQuizResult['addiction_type']); ?>
                                    </span>
                                </p>
                                <div class="row">
                                    <div class="col-6">
                                        <a href="/quiz.php" class="btn c-btn c-btn__dashboard w-100">Retake Assessment</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="/resources.php" class="btn c-btn c-btn__dashboard w-100">View Recommended Resources</a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="mt-3">You haven't completed an assessment yet.</p>
                            <a href="/quiz.php" class="btn c-btn c-btn__dashboard w-100">Take the Quiz</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Section: Victory Counter -->
            <div class="c-user__item c-user__item--4">
                <div class="card c-profile__card h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5><i class="bi bi-trophy c-user__icon text-warning display-6"></i></h5>
                        <h2 class="display-4 fw-bold mt-2"><?php echo $totalCheckins; ?></h2>
                        <p class="text-uppercase tracking-wider">Victories Logged</p>
                        <p class="small text-muted">"For though the righteous fall seven times, they rise again." <br>— Prov 24:16</p>
                    </div>
                </div>
            </div>
            <!-- Section: Journal -->
            <div class="c-user__item c-user__item--5">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-journal-richtext me-2"></i> Daily Journal</h5>
                        <p class="small text-muted mb-3">Reflect on your walk with Christ. Be honest about triggers or temptations.</p>
                        <form id="journalForm">
                            <textarea id="journalContent" class="form-control mb-3" rows="5" placeholder="How are you feeling today? Any specific struggles or praises?" required></textarea>

                            <!-- Toggle for Addiction Relation -->
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="isAddictionRelated">
                                <label class="form-check-label small" for="isAddictionRelated">
                                    Is this entry related to your addiction?
                                </label>
                            </div>

                            <!-- List of Addiction Types (Hidden by default) -->
                            <div id="addictionTypeSelection" class="mb-3" style="display: none;">
                                <p class="small text-muted mb-1">Select focus area(s):</p>
                                <div class="row row-cols-2 g-1">
                                    <?php
                                        $availableAddictions = ['Pornography', 'Alcohol', 'Drugs', 'Gambling', 'Digital', 'Smoking', 'Food'];
                                    foreach ($availableAddictions as $type): ?>
                                        <div class="col">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="addiction_types[]" value="<?php echo $type; ?>" id="check_<?php echo $type; ?>">
                                                <label class="form-check-label small" for="check_<?php echo $type; ?>"><?php echo $type; ?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <button type="submit" class="btn c-btn c-btn__create w-100">Save Entry</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Section: Coming Soon -->
            <div class="c-user__item c-user__item--6">
                <div class="card c-profile__card h-100 opacity-75 bg-light">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <i class="bi bi-lock-fill display-6 text-muted"></i>
                            <h6 class="mt-2 text-muted">Future Feature</h6>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Section: Recent Messages -->
            <div class="c-user__item c-user__item--7">
                <div class="card c-profile__card h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-chat-dots me-2"></i> Messages</h5>
                        <ul class="list-group list-group-flush mt-2">
                            <?php if ($recentInbox): ?>
                                <?php foreach ($recentInbox as $msg): ?>
                                    <li class="list-group-item px-0">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold small text-muted">From User #<?php echo (int) $msg['sender_id']; ?></span>
                                            <span class="small text-muted"><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                                        </div>
                                        <p class="mb-0 small text-truncate"><?php echo htmlspecialchars($msg['content']); ?></p>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item px-0 text-muted small">Your inbox is currently empty.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Section: Coming Soon -->
            <div class="c-user__item c-user__item--8">
                <div class="card c-profile__card h-100 opacity-75 bg-light">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <i class="bi bi-lock-fill display-6 text-muted"></i>
                            <h6 class="mt-2 text-muted">Future Feature</h6>
                        </div>
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
                            <a class="c-footer__links" href="/index.php">Home</a>
                        </li>
                        <li class="c-footer__item">
                            <a class="c-footer__links" href="/resources.php">Resources</a>
                        </li>
                        <li class="c-footer__item">
                            <a class="c-footer__links" href="/about.php">About</a>
                    </li>
                    <li class="c-footer__item">
                        <a class="c-footer__links" href="/contact.php">Contact</a>
                    </li>
                </ul>
            </div>
            <!-- Footer Content: Policies -->
            <div class="row">
                <div class="col-md-3">
                    <li class="c-footer__item">
                            <a class="c-footer__links" href="/policies.php?slug=terms">Terms of Service</a>
                        </li>
                </div>
                <div class="col-md-3">
                    <li class="c-footer__item">
                            <a class="c-footer__links" href="/policies.php?slug=privacy">Privacy Policy</a>
                        </li>
                </div>
                <div class="col-md-3">
                    <li class="c-footer__item">
                            <a class="c-footer__links" href="/policies.php?slug=cookie">Cookie Policy</a>
                        </li>
                </div>
            </div>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/auth.js"></script>
<script src="../assets/js/journal.js"></script>
</body>
</html>
