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
    $user_role   = 'user';
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
            $user_role   = $user_data['role'] ?? 'user';
            $user_link   = ($user_role === 'admin') ? '../admin/profile.php' : 'profile.php';
        } else {
            // Logged-in session exists, but user not found in DB (session cleanup needed)
            unset($_SESSION['user_id']);
            unset($_SESSION['logged_in']);
            $is_logged_in = false;
            header("Location: ../api/auth/register.php");
            exit();
        }
    }

    // --- Fetch User Data for Dashboard Display ---
    $user_data = FaithGuardRepository::getUserById($userId);

    if (! $user_data) {
        unset($_SESSION['user_id']);
        unset($_SESSION['logged_in']);
        header('Location: ../../index.php');
        exit;
    }

    $accountName = htmlspecialchars($user_data['name'] ?? $user_data['email']);
    $user_role   = $user_data['role'] ?? 'user';

    // Navbar link logic
    $profile_link = ($user_role === 'admin') ? 'api/admin/profile.php' : 'api/users/profile.php';

    // --- Fetch Dynamic Data ---

    // DIV 1: Progress Log
    $progressLogs   = FaithGuardRepository::getProgressLogsByUserId($userId);
    $recentCheckins = array_slice($progressLogs, 0, 5);
    $totalCheckins  = count($progressLogs);

    // DIV 2: Latest Quiz Result
    $latestQuizResult = FaithGuardRepository::getQuizResultsByUserId($userId);
    $latestQuizResult = $latestQuizResult[0] ?? null;

    // DIV 3: Recent Inbox Messages
    $recentInboxMessages = FaithGuardRepository::getInboxByUserId($userId);
    $recentInboxMessages = array_slice($recentInboxMessages, 0, 5);

    // Stats
    $recentPosts = FaithGuardRepository::getPostsByUserId($userId);
    $totalPosts  = count($recentPosts);
    $memberSince = date('d M, Y', strtotime($user_data['created_at']));
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
                        <span class="c-dropdown__text">Welcome                                                               <?php echo $accountName; ?></span>
                    </button>
                    <!-- LOGGED-IN DROPDOWN MENU -->
                    <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu" aria-labelledby="userDropdown">
                        <li>
                            <h6 class="dropdown-header c-dropdown__header">Signed in as:                                                                                         <?php echo ucfirst($user_role); ?></h6>
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
                            <a class="dropdown-item c-dropdown__item" href="../api/auth/register.php">
                                <i class="bi bi-person-plus me-2"></i>
                                <span class="c-dropdown__text js-create">Create Account</span>
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
        <section class="c-user__profile">
            <!-- GRID AREA: TITLE -->
            <div class="c-user__item c-user__item--title">
                <h2 class="c-main__title">User Dashboard</h2>
                <p class="text-muted">Manage your progress, track your assessments, and connect with the community.</p>
            </div>
            <!-- GRID AREA: STATS (Personal Profile) -->
            <div class="c-user__item c-user__item--stats">
                <div class="c-profile__items card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-person-circle me-2"></i> Account Summary</h5>
                        <ul class="list-group list-group-flush mt-3">
                            <li class="list-group-item"><strong>Email:</strong>                                                                                                                                                               <?php echo htmlspecialchars($user_data['email']); ?></li>
                            <li class="list-group-item"><strong>Member Since:</strong>                                                                                                                                                                             <?php echo $memberSince; ?></li>
                            <li class="list-group-item"><strong>Total Posts:</strong>                                                                                                                                                                           <?php echo $totalPosts; ?></li>
                            <li class="list-group-item"><strong>Role:</strong>                                                                                                                                                             <?php echo ucfirst($user_role); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- GRID AREA: PROGRESS (Check-ins) -->
            <div class="c-user__item c-user__item--progress">
                <div class="c-profile__items card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-clipboard-check me-2"></i> Accountability Progress</h5>
                        <p class="card-text text-muted">You have recorded <strong><?php echo $totalCheckins; ?></strong> check-ins.</p>
                        <ul class="list-group list-group-flush">
                            <?php if (! empty($recentCheckins)): ?>
                                <?php foreach ($recentCheckins as $log): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?php echo date('d/M/Y', strtotime($log['checkin_date'])); ?></span>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($log['milestone'] ?? 'Check-in'); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No check-ins yet. Start today!</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="../templates/progress.html" class="btn btn-sm btn-success">View Full Progress</a>
                        <a href="../api/progress/checkin.php" class="btn btn-sm btn-warning">New Check-in</a>
                    </div>
                </div>
            </div>

            <!-- GRID AREA: QUIZ (Latest Result) -->
            <div class="c-user__item c-user__item--quiz">
                <div class="c-profile__items card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-journal-check me-2"></i> Latest Assessment</h5>
                        <?php if ($latestQuizResult): ?>
                            <p class="mb-1"><strong>Date:</strong><?php echo date('d/M/Y', strtotime($latestQuizResult['created_at'])); ?></p>
                            <p class="mb-1"><strong>Score:</strong>                                                                                                                                       <?php echo htmlspecialchars($latestQuizResult['total_score']); ?></p>
                            <p class="text-danger mb-3"><strong>Focus Area:</strong>                                                                                                                                                                         <?php echo htmlspecialchars($latestQuizResult['addiction_type']); ?></p>
                            <a href="../templates/resources.html" class="btn btn-sm btn-info">Recommended Resources</a>
                        <?php else: ?>
                            <p class="card-text">Take the quiz to get personalized recommendations.</p>
                            <a href="../templates/quiz.html" class="btn btn-sm btn-warning">Take Quiz</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- GRID AREA: MESSAGES (Inbox) -->
            <div class="c-user__item c-user__item--messages">
                <div class="c-profile__items card c-profile__card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-envelope-open me-2"></i> Recent Messages</h5>
                        <ul class="list-group list-group-flush">
                            <?php if (! empty($recentInboxMessages)): ?>
                                <?php foreach ($recentInboxMessages as $message): ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <strong>From: User                                                                                                                             <?php echo htmlspecialchars($message['sender_id']); ?></strong>
                                            <small class="text-muted"><?php echo date('d/M', strtotime($message['created_at'])); ?></small>
                                        </div>
                                        <small class="text-muted d-block text-truncate"><?php echo htmlspecialchars($message['content']); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">Your inbox is empty.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="../templates/community.html#messages" class="btn btn-sm btn-primary">Go to Inbox</a>
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
</html>
