<?php
    session_set_cookie_params([
        'lifetime' => 302400, // 3.5 days (84 hours)
        'path'     => '/',
        'domain'   => $_SERVER['HTTP_HOST'] ?? '',
        'secure'   => true,
        'httponly' => true,
    ]);
    session_start();

    // --- Core App Requirements (Always required) ---
    require_once __DIR__ . "/db/database.php";
    require_once __DIR__ . "/db/FaithGuardRepository.php";
    // --- Optional Helper/Debug (Required, but note its function) ---
    require_once __DIR__ . "/api/helper/debug.php";

    $is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

    // --- CRITICAL FIX: Define user variables needed for navigation bar ---
    $user        = null;
    $accountName = 'Guest';
    $user_role   = 'user';

    if ($is_logged_in && isset($_SESSION['user_id'])) {
        // Fetch user data using the repository method
        $user_data = FaithGuardRepository::getUserById($_SESSION['user_id']);

        if ($user_data) {
            $user = true;
            // Assuming your 'users' table has a 'name' or 'email' column and a 'role' column
            $accountName = htmlspecialchars($user_data['name'] ?? $user_data['email']);
            $user_role   = $user_data['role'] ?? 'user';
        } else {
            // Logged-in session exists, but user not found in DB (session cleanup needed)
            unset($_SESSION['user_id']);
            unset($_SESSION['logged_in']);
            $is_logged_in = false;
        }
    }

    // --- Fetch Resources for Dynamic Display ---
    // Fetch all resources from DB
    $resources     = FaithGuardRepository::getAllResources();
    // Limit to 6 for display (adjust as needed)
    $max_resources = 6;
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
                        <span class="c-dropdown__text">Welcome                                                                                                                                                                                                                                                         <?php echo $accountName; ?></span>
                    </button>
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
                            <a class="dropdown-item c-dropdown__item" href="<?php echo($user_role === 'admin') ? 'api/admin/profile.php' : 'api/users/profile.php'; ?>">
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
                            <input type="text" id="signupName" class="form-control c-dropdown__info mb-2" placeholder="First Name">
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
    <!-- Hero Content -->
    <header class="c-hero">
        <div class="container-fluid">
            <div class="c-hero__content text-center">
                <h1 class="c-hero__title">Welcome to FaithGuard</h1>
                <h2 class="c-hero__subtitle">Faith-Based Resources for Recovery</h2>
                <p class="c-hero__text">Free, vetted resources to support your journey toward spiritual freedom. Rooted in Christian hope and redemption.</p>
                <a href="templates/quiz.html" class="btn c-btn c-hero__btn">Take the Quiz</a>
            </div>
        </div>
    </header>
    <!-- Main Content -->
    <main class="c-main container my-5">
        <!-- Resource Section -->
        <section class="c-main__section mb-5">
            <h2 class="c-main__title">Our Mission</h2>
            <p class="c-main__text">At FaithGuard, we are dedicated to providing resources and tools to help individuals regain and protect their faith from digital influenced addictions like porn.</p>
        </section>
        <!-- Featured Resources Section -->
        <section class="c-main__section mb-5">
            <h2 class="c-main__title">Featured Resources</h2>
            <div class="row c-resources__list">
                <?php if (! empty($resources)): ?>
                    <?php $count = 0; ?>
                    <?php foreach ($resources as $resource): ?>
                        <?php if ($count >= $max_resources) {
                                break;
                            }
                        // Limit to max_resources ?>
                        <div class="col-md-4 col-12 mb-4">
                            <div class="card c-card">
                                <div class="card-body c-card__body">
                                    <h5 class="card-title c-card__title"><?php echo htmlspecialchars($resource['title']); ?></h5>
                                    <p class="card-text c-card__text"><?php echo htmlspecialchars(substr($resource['content'], 0, 100)) . (strlen($resource['content']) > 100 ? '...' : ''); ?></p>
                                    <a href="templates/resources.html?id=<?php echo $resource['id']; ?>" class="btn c-btn c-card__btn">Learn More</a>
                                </div>
                            </div>
                        </div>
                        <?php $count++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center">No resources available yet. Check back soon!</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <!-- Community Impact Section -->
    <article class="container-fluid mb-4">
        <section class="c-main__section c-impact mb-5">
            <h2 class="c-impact__title text-center">Community Impact</h2>
            <p class="c-impact__intro text-center">Hear from those who have found hope and strength through FaithGuard's faith-based resources.</p>
            <div class="row">
                <!-- Static input, get's changed in PHP -->
                <div class="col-md-4 col-12 mb-4">
                    <div class="card c-card c-impact__card">
                        <div class="card-body c-card__body c-impact__body">
                            <blockquote class="c-impact__quote">"FaithGuard's devotionals helped me rebuild my relationship with God after years of struggle. I'm free now."</blockquote>
                            <cite class="c-impact__cite">- Anonymous User</cite>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </article>
    <!-- Footer -->
    <div class="c-footer--placeholder"></div>
</body>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<!-- Custom JS -->
<script src="assets/js/auth.js"></script>
<script src="assets/js/footer.js"></script>
<script src="assets/js/resources.js"></script>
</html>