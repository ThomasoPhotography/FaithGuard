<?php
    declare (strict_types = 1);

    /* =========================================================
   SESSION + SECURITY
========================================================= */
    session_set_cookie_params([
    'lifetime' => 302400,
    'path'     => '/',
    'domain'   => $_SERVER['SERVER_NAME'] ?? '',
    'secure'   => true,
    'httponly' => true,
    ]);
    session_start();

    require_once __DIR__ . "/db/database.php";
    require_once __DIR__ . "/db/FaithGuardRepository.php";
    require_once __DIR__ . "/api/helper/debug.php";
    require_once __DIR__ . "/api/helper/user.php";

    /* =========================================================
   AUTH GUARD
========================================================= */
    $is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

    $user        = null;
    $accountName = 'Guest';
    $user_role   = 'user';

    if ($is_logged_in && isset($_SESSION['user_id'])) {
    $user_data = FaithGuardRepository::getUserById($_SESSION['user_id']);
    if ($user_data) {
        $user        = normalize_user($user_data);
        $accountName = $user['display_name'] ?? ($user['email'] ?? 'User');
        $user_role   = $user['role'] ?? 'user';
    } else {
        session_destroy();
        header("Location: /register.php");
        exit;
    }
    }
    /* =========================================================
   USER CONTEXT
========================================================= */
    $userId = $_SESSION['user_id'] ?? null;
    if (! $userId) {
    session_destroy();
    header("Location: /index.php");
    exit;
    }

    // Ensure account name and role come from normalized user
    $accountName = $accountName ?? ($user['display_name'] ?? ($user['email'] ?? 'User'));
    $user_role   = $user_role ?? ($user['role'] ?? 'user');

    /* =========================================================
   QUIZ DATA
========================================================= */
    $questions = FaithGuardRepository::getAllQuizQuestions();

    $addictionQuestion = null;
    foreach ($questions as $q) {
    if ((int) $q['id'] === 1) {
        $addictionQuestion = $q;
        break;
    }
    }

    if (! $addictionQuestion) {
    throw new RuntimeException('Addiction selection question (ID 1) missing.');
    }

    /* =========================================================
   ADDICTION TYPES (still static by design)
========================================================= */
    $addictionTypes = [
    'Pornography'            => 'Pornography',
    'Alcohol'                => 'Alcohol',
    'Substance Use'          => 'Substance',
    'Gambling'               => 'Gambling',
    'Digital / Social Media' => 'Digital',
    'Smoking / Vaping'       => 'Smoking',
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FaithGuard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="icon" href="assets/uploads/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
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
    <main class="c-main container my-5">
    <section class="c-main__section c-quiz">
        <h1 class="c-main__title text-center mb-3">FaithGuard Self-Assessment</h1>
        <p class="c-main__text text-center mb-5">
            Answer honestly. This assessment helps guide you toward Scripture-rooted support.
        </p>
        <div class="c-progress mb-4">
            <div class="c-progress__track">
                <div class="c-progress__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
            </div>
        </div>
        <form class="c-quiz__content c-quiz__form" action="/api/quiz/submit.php" method="POST">

            <!-- =====================================================
                ADDICTION SELECTION (ID 1)
            ===================================================== -->
            <section class="c-quiz__question" data-step="0" data-question-id="<?php echo (int) $addictionQuestion['id']; ?>" data-required="true">
                <h4 class="mb-2">
                    <?php echo htmlspecialchars($addictionQuestion['question'], ENT_QUOTES, 'UTF-8'); ?>
                </h4>
                <?php if (! empty($addictionQuestion['description'])): ?>
                    <p class="text-muted mb-3">
                        <?php echo htmlspecialchars($addictionQuestion['description'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                <?php endif; ?>
                <?php foreach ($addictionTypes as $label => $value): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input js-addiction-checkbox" type="checkbox" name="addiction_types[]" id="addiction_<?php echo htmlspecialchars($value, ENT_QUOTES); ?>" value="<?php echo htmlspecialchars($value, ENT_QUOTES); ?>">
                        <label class="form-check-label" for="addiction_<?php echo htmlspecialchars($value, ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </section>

            <!-- =====================================================
                ALL QUESTIONS EXCEPT ID 1
            ===================================================== -->
            <?php $stepIndex = 1;foreach ($questions as $q): $questionId = (int) $q['id'];if ($questionId === 1) {continue;}
                $answerOptions              = FaithGuardRepository::getAllQuizAnswerOptions($questionId); ?>
                <section class="c-quiz__question" data-step="<?php echo $stepIndex; ?>" data-question-id="<?php echo $questionId; ?>" data-required="true">
                    <h5 class="mb-3">
                        <?php echo htmlspecialchars($q['question'], ENT_QUOTES, 'UTF-8'); ?>
                    </h5>
                    <?php foreach ($answerOptions as $optIndex => $option): ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[<?php echo $questionId; ?>]" value="<?php echo htmlspecialchars($option['id'] ?? $optIndex, ENT_QUOTES); ?>" required>
                            <label class="form-check-label">
                                <?php echo htmlspecialchars($option['label'] ?? ($option['value'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php $stepIndex++;endforeach; ?>
            <!-- =====================================================
                NAVIGATION
            ===================================================== -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" id="prevButton" class="btn c-btn">Back</button>
                <button type="button" id="nextButton" class="btn c-btn">Next</button>
                <button type="submit" id="submitButton" class="btn c-btn" hidden>
                    <?php
                        if (! isset($userId)) {
                            throw new RuntimeException('Quiz submission error: User not authenticated.');
                        } else {
                            echo 'Submit Assessment' . 'ID: ' . htmlspecialchars((string) $userId, ENT_QUOTES, 'UTF-8');
                        }
                    ?>
                </button>
            </div>
        </form>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/quiz.js"></script>
<script src="assets/js/auth.js"></script>
</body>
</html>
