<?php
    session_set_cookie_params([
        'lifetime' => 302400, // 3.5 days (84 hours)
        'path'     => '/',
        'domain'   => $_SERVER['SERVER_NAME'] ?? '',
        'secure'   => true,
        'httponly' => true,
    ]);
    session_start();

    // --- Core App Requirements (Always required) ---
    require_once __DIR__ . "/db/database.php";
    require_once __DIR__ . "/db/FaithGuardRepository.php";
    // --- Optional Helper/Debug (Required, but note its function) ---
    require_once __DIR__ . "/api/helper/debug.php";
    // --- INITIALIZE VARIABLES ---
    $is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

    // --- CRITICAL FIX: Define user variables needed for navigation bar ---
    $user         = null;
    $accountName  = 'Guest';
    $user_role    = 'user';
    $profile_link = '';
    $user_data    = null;

    // Check login status and fetch user data
    if ($is_logged_in && isset($_SESSION['user_id'])) {
        $user_data = FaithGuardRepository::getUserById($_SESSION['user_id']);

        if ($user_data) {
            $user        = true;
            $accountName = htmlspecialchars($user_data['name'] ?? $user_data['email']);
            $user_role   = $user_data['role'] ?? 'user';

            // Set Role-Based Profile Link
            if ($user_role === 'admin') {
                $profile_link = 'api/admin/profile.php';
            } else {
                $profile_link = 'api/users/profile.php';
            }
        } else {
            // Logged-in session exists, but user not found in DB
            unset($_SESSION['user_id']);
            unset($_SESSION['logged_in']);
            $is_logged_in = false;
        }
    }

    // Redirect if not logged in (Quiz is for logged-in users)
    if (! $is_logged_in) {
        // Optional: Redirect to register or login page instead of index
        header("Location: index.php");
        exit();
    }

    $message = '';
    $score = 0;
    $total = 0;

    // Fetch questions from DB (PHP array)
    $questions = FaithGuardRepository::getAllQuizQuestions();
    $total = count($questions);

        // Handle form submission (POST to self)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answers'])) {
        $answers = $_POST['answers'];
        foreach ($questions as $q) {
            if (isset($answers[$q['id']]) && $answers[$q['id']] === $q['correct_answer']) {
                $score++;
            }
        }

        // Save result to DB
        $saved = FaithGuardRepository::saveQuizResult($_SESSION['user_id'], $_SESSION['quiz_id'], $answers, $score);
        $message = $saved ? "Quiz completed! Your score: $score/$total" : "Error saving results.";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FaithGuard Quiz - Addiction Assessment">
    <meta name="keywords" content="FaithGuard, Quiz, Addiction, Assessment">
    <meta name="author" content="WWTW - FaithGuard">
    <meta name="robots" content="noindex">
    <!-- Title -->
    <title>FaithGuard - Quiz</title>
    <!-- Favicon -->
    <link rel="icon" href="assets/uploads/favicon.ico" type="image/x-icon">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/main.css">
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

    <!-- Quiz Section -->
    <main class="c-main container my-5">
        <section class="c-quiz">
            <h2 class="c-quiz__title text-center">Addiction Assessment</h2>
            <div class="row justify-content-center">
                <div class="col-md-8 col-12">
                    <div class="card c-card">
                        <div class="card-body c-card__body">
                            <?php if ($message): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                            <?php else: ?>
                                <!-- Progress Bar -->
                                <div class="progress c-progress mb-4">
                                    <div class="progress-bar c-progress__bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>

                                <!-- Quiz Form Container -->
                                <form id="quizForm" method="POST">
                                    <div class="c-quiz__content mb-4">
                                        <?php foreach ($questions as $index => $q): ?>
                                            <div class="c-quiz__question" data-step="<?php echo $index; ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>">
                                                <h5><?php echo ($index + 1) . '. ' . htmlspecialchars($q['question']); ?></h5>
                                                <?php $options = json_decode($q['options'], true); ?>
                                                <?php foreach ($options as $opt): ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="answers[<?php echo $q['id']; ?>]" value="<?php echo htmlspecialchars($opt); ?>" required>
                                                        <label class="form-check-label"><?php echo htmlspecialchars($opt); ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" id="prevButton" class="btn c-btn c-btn__quiz c-btn__quiz--prev">Previous</button>
                                        <button type="button" id="nextButton" class="btn c-btn c-btn__quiz c-btn__quiz--next">Next</button>
                                        <button type="submit" id="submitButton" class="btn c-btn c-btn__quiz c-btn__quiz--submit">Submit Quiz</button>
                                    </div>
                                </form>
                            <?php endif; ?>
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
</body>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<!-- Custom JS -->
<script src="assets/js/cookie-banner.js"></script>
<script src="assets/js/auth.js"></script>
<script src="assets/js/quiz.js"></script>
</html>