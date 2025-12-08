<?php
    // --- Core App Requirements (Always required) ---
    require_once __DIR__ . "/../../db/database.php";
    require_once __DIR__ . "/../../db/FaithGuardRepository.php";
    require_once __DIR__ . "/../helper/debug.php";
    session_set_cookie_params([
        'lifetime' => 86400, // 1 day
        'path'     => '/',
        'domain'   => $_SERVER['HTTP_HOST'] ?? '',
        'secure'   => true,
        'httponly' => true,
    ]);
    session_start();
    // --- INITIALIZE VARIABLES ---
    $is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    $user_role    = 'user';
    $user_data    = null;
    $accountName  = '';
    $profile_link = '';
    if ($is_logged_in && isset($_SESSION['user_id'])) {
        $user_data = FaithGuardRepository::getUserById($_SESSION['user_id']);
        if ($user_data) {
            $user_role   = $user_data['role'] ?? 'user';
            $accountName = htmlspecialchars($user_data['name'] ?? $user_data['email']);
            // Set Role-Based Profile Link (Correct for this file location)
            if ($user_role === 'admin') {
                $profile_link = 'api/admin/profile.php';
            } else {
                $profile_link = 'api/users/profile.php';
            }
        }
    }
    if (! $is_logged_in || $user_role !== 'user' || ! $user_data) {
        header('Location: ../../index.php');
        exit;
    }
    // --- Fetch $userId from session ---
    $userId = $_SESSION['user_id'];

    // --- Fetch Dynamic Data for Dashboard DIVs ---
    //Progress Log (Recent Check-ins)
    $progressLogs   = FaithGuardRepository::getProgressLogsByUserId($userId);
    $recentCheckins = array_slice($progressLogs, 0, 5);
    $totalCheckins  = count($progressLogs);
    //Latest Quiz Result
    $latestQuizResult = FaithGuardRepository::getQuizResultsByUserId($userId);
    $latestQuizResult = $latestQuizResult[0] ?? null;
    //User Activity (Posts and Prayers) - Keeping the logic for compatibility, although it won't be displayed in DIV 4
    $recentPosts   = FaithGuardRepository::getPostsByUserId($userId);
    $recentPrayers = FaithGuardRepository::getPrayersByUserId($userId);
    $totalPosts    = count($recentPosts);
    //Messaging - Fetch recent INBOX messages
    $recentInboxMessages = FaithGuardRepository::getInboxByUserId($userId);
    $recentInboxMessages = array_slice($recentInboxMessages, 0, 5); // Limit to 5 recent
                                                                    // General Stats
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
    <title>FaithGuard</title>
    <!-- Favicon -->
    <link rel="icon" href="../../assets/uploads/favicon.ico" type="image/x-icon">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="../../assets/css/main.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light c-nav">
        <div class="container-fluid">
            <!-- LEFT SIDE: LOGO + BRAND -->
            <a class="navbar-brand c-nav__brand" href="../../index.php">
                <img src="../../assets/uploads/FaithGuard_Primary_Logo.svg" alt="FaithGuard Logo" class="c-nav__logo">
            </a>
            <button class="navbar-toggler c-nav__toggler c-nav__toggler--btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Navigation Links (CENTER/LEFT) -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item c-nav__item"><a class="nav-link c-nav__link" href="../../templates/community.html">Community</a></li>
                    <li class="nav-item c-nav__item"><a class="nav-link c-nav__link" href="../../templates/progress.html">Progress</a></li>
                    <li class="nav-item c-nav__item"><a class="nav-link c-nav__link" href="../../templates/resources.html">Resources</a></li>
                </ul>

                <!-- RIGHT SIDE: USER DROPDOWN (Assuming user is logged in here) -->
                <div class="d-flex dropdown c-dropdown">
                    <button class="btn c-btn c-dropdown__btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="c-dropdown__icon bi bi-person-check me-1"></i>
                        <span class="c-dropdown__text">Welcome                                                                                                                                                                                                                                                                                                                                                                                     <?php echo $accountName; ?></span>
                    </button>
                    <!-- LOGGED-IN DROPDOWN MENU -->
                    <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu" aria-labelledby="userDropdown">
                        <li><h6 class="dropdown-header c-dropdown__header">Signed in as:                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <?php echo ucfirst($user_role); ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item c-dropdown__item" href="<?php echo $profile_link; ?>"><i class="bi bi-person-badge me-2"></i> Profile / Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item c-dropdown__item js-logout-btn" href="#" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- Main -->
    <main class="c-main container my-5">
        <h2 class="c-main__title">Welcome Back,                                                                                                                                                                                             <?php echo $accountName; ?></h2>
        <p class="text-muted">This is your personal dashboard for tracking progress and accessing core tools.</p>

        <section class="c-profile c-profile__users row">

            <!-- Personal Profile and Stats -->
            <div class="col-md-6 col-12 mb-4">
                <div class="c-profile__items card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-person-circle me-2"></i>
                            <span>Account Summary</span>
                        </h5>
                        <ul class="list-group list-group-flush mt-3">
                            <li class="list-group-item">
                                <strong>Name:</strong>
                                <?php echo ucfirst($user_data['name']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Email:</strong>
                                <?php echo htmlspecialchars($user_data['email']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Member Since:</strong>
                                <?php echo $memberSince; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Total Posts:</strong>
                                <?php echo $totalPosts; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Progress Log (Required Section) -->
            <div class="col-md-6 col-12 mb-4">
                <div class="c-profile__items card c-progress__card h-100">
                    <div class="card-body c-progress__cardbody">
                        <h5 class="card-title c-progress__cardtitle">
                            <i class="bi bi-clipboard-check me-2"></i>
                            <span>Accountability Progress</span>
                        </h5>
                        <p class="card-text text-muted">You have recorded</p><span class="c-progress__count"><?php echo $totalCheckins; ?></span> <p class="card-text text-muted">check-ins so far.</p>

                        <ul class="list-group list-group-flush">
                            <?php if (! empty($recentCheckins)): ?>
                                <?php foreach ($recentCheckins as $log): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-success fw-bold">Check-in:</span>
                                        <?php echo date('d/M/Y', strtotime($log['checkin_date'])); ?>
                                        <span class="badge bg-secondary">
                                            <?php echo htmlspecialchars($log['milestone'] ?? 'Standard'); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No progress logs recorded yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="../../templates/progress.html" class="btn btn-sm c-btn c-btn__dashboard">View Full Progress</a>
                        <a href="../../api/progress/checkin.php" class="btn btn-sm c-btn c-btn__create">New Check-in</a>
                    </div>
                </div>
            </div>

            <!-- Latest Quiz Results and Recommendation -->
            <div class="col-md-6 col-12 mb-4">
                <div class="c-profile__items card c-quiz__card h-100">
                    <div class="card-body c-quiz__cardbody">
                        <h5 class="card-title c-quiz__cardtitle">
                            <i class="bi bi-journal-check me-2"></i>
                            <span>Latest Assessment</span>
                        </h5>

                        <?php if ($latestQuizResult): ?>
                            <p class="card-text c-quiz__cardtext mb-1">
                                **Last Quiz Taken:** <span class="c-quiz__infograph"><?php echo date('d/M/Y', strtotime($latestQuizResult['created_at'])); ?></span>
                            </p>
                            <p class="card-text c-quiz__cardtext mb-1">
                                **Score:** <span class="fw-bold c-quiz__infograph"><?php echo htmlspecialchars($latestQuizResult['total_score']); ?></span>
                            </p>
                            <p class="card-text text-danger c-quiz__cardtext mb-3">
                                **Identified Area:** <span class="c-quiz__infograph"><?php echo htmlspecialchars($latestQuizResult['addiction_type']); ?></span>
                            </p>
                            <p class="mt-3">
                                <a href="../../templates/resources.html" class="btn btn-sm c-quiz__btn">View Recommended Resources</a>
                            </p>
                        <?php else: ?>
                            <p class="card-text c-quiz__cardtext">Take the initial quiz to unlock personalized resource recommendations and tools.</p>
                            <a href="../../templates/quiz.html" class="btn btn-sm c-btn c-btn__dashboard">Take Quiz Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Recent Messaging (INBOX) -->
            <div class="col-md-6 col-12 mb-4">
                <div class="c-profile__items card c-message__card h-100">
                    <div class="card-body c-message__cardbody">
                        <h5 class="card-title c-message__cardtitle">
                            <i class="bi bi-envelope-open me-2"></i>
                            <span>Recent Messages</span>
                        </h5>
                        <p class="card-text c-message__cardtext">Quick view of your last 5 messages received from the community or admin.</p>

                        <ul class="list-group list-group-flush">
                            <?php if (! empty($recentInboxMessages)): ?>
                                <?php foreach ($recentInboxMessages as $message): ?>
                                    <?php
                                        // You might need a helper method to resolve the sender ID to a name/email for display
                                        $senderName     = $message['sender_id'] === $userId ? 'You' : 'User ' . $message['sender_id'];
                                        $contentPreview = htmlspecialchars(substr($message['content'], 0, 40)) . '...';
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="bi bi-person me-2"></i> From: <span class="c-message__sender"><?php echo $senderName; ?></span>
                                        </span>
                                        <small class="text-muted"><?php echo date('d/M', strtotime($message['created_at'])); ?></small>
                                    </li>
                                    <li class="list-group-item py-1 small text-truncate">
                                        <?php echo $contentPreview; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">Your inbox is empty!</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="../../templates/community.html#messages" class="btn btn-sm c-btn c-btn__dashboard">View Full Inbox</a>
                        <a href="../../api/messages/send.php" class="btn btn-sm c-btn c-btn__create">Send Message</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer -->
    <div class="c-footer--placeholder"></div>
</body>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<!-- Custom JS -->
<script src="../../assets/js/auth.js"></script>
<script src="../../assets/js/footer.js"></script>
</html>
