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
    $accountName  = 'Admin';
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

    if (! $is_logged_in || $user_role !== 'admin' || ! $user_data) {
        header('Location: ../../index.php');
        exit;
    }

    // --- Fetch Dynamic Data ---
    // --- Flagged Reports ---
    $reports = [
        ['post_id' => 901, 'reason' => 'Hate speech'],
        ['post_id' => 902, 'reason' => 'Spam link'],
        ['post_id' => 903, 'reason' => 'Inappropriate content'],
    ]; 
    $reports = array_slice($reports, 0, 5); 

    // --- Resource Count ---
    $allResources = FaithGuardRepository::getAllResources();
    $resourceCount = count($allResources);

    // --- Legal texts ---
    $tosText = 'Terms of Service text fetched successfully and rendered here.';
    $privacyText = 'Privacy Policy content fetched successfully and rendered here.';

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
            <a class="navbar-brand c-nav__brand" href="../../index.php">  <!-- Fixed path -->
                <img src="../../assets/uploads/FaithGuard_Primary_Logo.svg" alt="FaithGuard Logo" class="c-nav__logo">
            </a>
            <button class="navbar-toggler c-nav__toggler c-nav__toggler--btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Navigation Links (CENTER/LEFT) -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item c-nav__item">
                        <a class="nav-link c-nav__link" href="../../templates/community.html">Community</a>
                    </li>
                    <li class="nav-item c-nav__item">
                        <a class="nav-link c-nav__link" href="../../templates/progress.html">Progress</a>
                    </li>
                    <li class="nav-item c-nav__item">
                        <a class="nav-link c-nav__link" href="../../templates/resources.html">Resources</a>
                    </li>
                </ul>

                <!-- RIGHT SIDE: USER/LOGIN DROPDOWN -->
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
                        <li>
                            <a class="dropdown-item c-dropdown__item" href="<?php echo $profile_link; ?>">
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
            </div>
        </div>
    </nav>
    <!-- Main -->
    <main class="c-main container my-5">
        <section class="c-profile">
            <h2 class="c-profile__title">Admin Dashboard</h2>
            <div class="row">
                <!-- Flagged/Reported Posts -->
                <div class="col-md-6 col-12 mb-4">
                    <div class="c-profile__items div1 card">
                        <h5 class="card-title">Flagged/Reported Posts (<?php echo count($reports); ?> Pending)</h5>
                        <p class="card-text">Preview and moderate reported community posts to maintain a safe, faith-focused environment.</p>
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($reports)): ?>
                                <?php foreach ($reports as $report): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Post ID: <?php echo htmlspecialchars($report['post_id']); ?> - Reason: <?php echo htmlspecialchars($report['reason']); ?>
                                        <button class="btn btn-sm btn-danger">Review</button>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No pending reports.</li>
                            <?php endif; ?>
                        </ul>
                        <a href="../admin/moderation.php" class="btn btn-primary mt-2">View All Reports</a>
                    </div>
                </div>
                <!-- Resource Management -->
                <div class="col-md-6 col-12 mb-4">
                    <div class="c-profile__items div2 card">
                        <h5 class="card-title">Resource Management</h5>
                        <p class="card-text">Quickly create new resources or view total resources available for users.</p>
                        <!-- Example: Mini form and stats -->
                        <form class="mb-3" action="../resources/create.php" method="POST">
                            <input type="text" name="title" class="form-control mb-2" placeholder="Resource Title" required>
                            <textarea name="content" class="form-control mb-2" placeholder="Content" rows="2" required></textarea>
                            <button type="submit" class="btn btn-success">Create Resource</button>
                        </form>
                        <p><strong>Total Resources:</strong> <?php echo $resourceCount; ?></p>
                        <a href="../resources/list.php" class="btn btn-secondary">Manage All Resources</a>
                    </div>
                </div>
                <!-- Legal & Policy Updates -->
                <div class="col-md-6 col-12 mb-4">
                    <div class="c-profile__items div3 card">
                        <h5 class="card-title">Legal & Policy Updates</h5>
                        <p class="card-text">Update Terms of Service and Privacy Policy to ensure compliance and user trust.</p>
                        
                        <!-- ToS Form -->
                        <form class="mb-3" action="../admin/legal.php" method="POST">
                            <label for="tos">Terms of Service</label>
                            <textarea name="tos_content" id="tos" class="form-control mb-2" rows="3"><?php echo htmlspecialchars($tosText); ?></textarea>
                            <button type="submit" name="update_tos" class="btn btn-warning">Update ToS</button>
                        </form>
                        
                        <!-- Privacy Form -->
                        <form action="../admin/legal.php" method="POST">
                            <label for="privacy">Privacy Policy</label>
                            <textarea name="privacy_content" id="privacy" class="form-control mb-2" rows="3"><?php echo htmlspecialchars($privacyText); ?></textarea>
                            <button type="submit" name="update_privacy" class="btn btn-warning">Update Privacy</button>
                        </form>
                    </div>
                </div>
                <!-- Admin Message Box (Recent Activity) -->
                <div class="col-md-6 col-12 mb-4">
                    <div class="c-profile__items div4 card">
                        <h5 class="card-title">Recent Admin Messages</h5>
                        <p class="card-text">Quickly view the last few messages sent by you (the admin).</p>
                        
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($recentMessages)): ?>
                                <?php foreach ($recentMessages as $message): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo date('H:i', strtotime($message['created_at'])); ?>: "<?php echo htmlspecialchars(substr($message['content'], 0, 30)); ?>..."
                                        <span class="badge bg-secondary">To: <?php echo htmlspecialchars($message['receiver_id']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No recent messages sent.</li>
                            <?php endif; ?>
                        </ul>
                        
                        <a href="../messages/send.php" class="btn btn-warning mt-2">Send New Message</a>
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
