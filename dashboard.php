<?php
    session_set_cookie_params([
    'lifetime' => 302400, // 3.5 days (84 hours)
    'path'     => '/',
    'domain'   => $_SERVER['SERVER_NAME'] ?? '',
    'secure'   => true,
    'httponly' => true,
    ]);
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }
    // --- Core Site Requirements (Always required) ---
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/db/Database.php';
    require_once __DIR__ . '/db/config.php';
    require_once __DIR__ . '/db/FaithGuardRepository.php';
    // --- Core Site Session Check (Always required) ---
    if (! isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
    }
    $is_logged_in   = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    $user           = FaithGuardRepository::getUserById($_SESSION['user_id']);
    $progress       = FaithGuardRepository::getUserProgress($_SESSION['user_id']);
    $checkins       = FaithGuardRepository::getUserCheckins($_SESSION['user_id'], 7);
    $lastQuizResult = FaithGuardRepository::getLastQuizResult($_SESSION['user_id']);
    if (! $user) {
    session_destroy();
    header('Location: /login.php');
    exit;
    }
    $isAdmin = ! empty($user['is_admin']);
    // Redirect admins to admin dashboard
    if ($isAdmin) {
    header('Location: /admin/dashboard.php');
    exit;
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
    <title>FaithGuard</title>
    <!-- Favicon -->
    <link rel="icon" href="/assets/uploads/favicon.ico" type="image/x-icon">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <!-- Nav -->
    <?php require_once __DIR__ . '/partials/nav.php'; ?>
    <!-- Dashboard Content -->
    <div class="c-dashboard">
        <div class="c-dashboard__container container">
            <header class="c-dashboard__header">
                <h1 class="c-dashboard__title"> Welcome, <?php echo htmlspecialchars($user['first_name'] ?: 'Friend'); ?>
            </h1>
            <p class="c-dashboard__subtitle">
                Every day is a new opportunity to walk forward.
            </p>
        </header>
        <!-- Progress Section -->
        <section class="c-dashboard__section c-dashboard__section--progress">
            <h2 class="c-dashboard__section-title">Progress</h2>
            <div class="c-dashboard__stats">
                <div class="c-dashboard__stat-card">
                    <span class="c-dashboard__stat-value"><?php echo $progress['streak_days'] ?? 0; ?></span>
                    <span class="c-dashboard__stat-label">Current Streak</span>
                </div>
                <div class="c-dashboard__stat-card">
                    <span class="c-dashboard__stat-value"><?php echo $progress['longest_streak'] ?? 0; ?></span>
                    <span class="c-dashboard__stat-label">Longest Streak</span>
                </div>
                <div class="c-dashboard__stat-card">
                    <span class="c-dashboard__stat-value"><?php echo $progress['total_checkins'] ?? 0; ?></span>
                    <span class="c-dashboard__stat-label">Total Check-ins</span>
                </div>
                <div class="c-dashboard__stat-card">
                    <span class="c-dashboard__stat-value"><?php echo count($checkins); ?></span>
                    <span class="c-dashboard__stat-label">This Week</span>
                </div>
            </div>
        </section>
        <!-- Main Grid -->
        <div class="c-dashboard__grid">
            <!-- Journal -->
            <section class="c-dashboard__section c-dashboard__section--journal">
                <div class="c-dashboard-card c-dashboard-card--journal">
                    <h3 class="c-dashboard-card__title">Journal</h3>
                    <p class="c-dashboard-card__text">
                        Reflect on your journey. Record struggles, victories, and prayers.
                    </p>
                    <a href="/journal.php" class="c-btn c-btn--primary c-dashboard-card__button">
                        Open Journal
                    </a>
                </div>
            </section>
            <!-- Quiz Assessment -->
            <section class="c-dashboard__section c-dashboard__section--quiz">
                <div class="c-dashboard-card c-dashboard-card--quiz">
                    <h3 class="c-dashboard-card__title">Quiz Assessment</h3>
                    <?php if ($lastQuizResult): ?>
                        <div class="c-dashboard-card__quiz-result">
                            <p class="c-dashboard-card__text">
                                Last Score: <strong><?php echo $lastQuizResult['total_score']; ?></strong>
                            </p>
                            <p class="c-dashboard-card__meta">
                                Taken on <?php echo date('M j, Y', strtotime($lastQuizResult['taken_at'])); ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <p class="c-dashboard-card__text">
                            You have not taken the assessment yet.
                        </p>
                    <?php endif; ?>
                    <a href="/quiz.php" class="c-btn c-btn--outline c-dashboard-card__button">
                        <?php echo $lastQuizResult ? 'Retake Assessment' : 'Take Assessment'; ?>
                    </a>
                </div>
            </section>
            <!-- Scripture Section -->
            <section class="c-dashboard__section c-dashboard__section--scripture">
                <div class="c-dashboard-card c-dashboard-card--scripture">
                    <h3 class="c-dashboard-card__title">Scripture</h3>
                    <form class="c-dashboard-scripture" id="c-dashboard-scripture-form">
                        <div class="c-dashboard-scripture__row">
                            <select name="book" class="c-dashboard-scripture__select">
                                <?php foreach (FaithGuardRepository::getAllBooks() as $book): ?>
                                    <option value="<?php echo htmlspecialchars($book['name']); ?>">
                                        <?php echo htmlspecialchars($book['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="chapter" class="c-dashboard-scripture__select">
                                <?php foreach (FaithGuardRepository::getBookChapters($bookId, $language) as $bookName => $chapters): ?>
                                    <optgroup label="<?php echo htmlspecialchars($bookName); ?>">
                                        <?php foreach ($chapters as $chapterNum): ?>
                                            <option value="<?php echo $chapterNum; ?>">
                                                <?php echo $chapterNum; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                            <select name="version" class="c-dashboard-scripture__select">
                                <option value="NRSVUE">NRSVUE</option>
                                <option value="NBV21">NBV21</option>
                            </select>
                        </div>
                        <button type="submit" class="c-btn c-btn--ghost c-dashboard-scripture__button">
                            Load Chapter
                        </button>
                    </form>
                    <div id="c-dashboard-scripture-content" class="c-dashboard-scripture__content">
                        Select a book and chapter to begin.
                    </div>
                </div>
            </section>
        </div>
        <!-- Admin Policy Management -->
        <?php if ($isAdmin): ?>
            <section class="c-dashboard__section c-dashboard__section--admin">
                <div class="c-dashboard-card c-dashboard-card--admin">
                    <h3 class="c-dashboard-card__title">Policy Management</h3>
                    <div class="c-dashboard-admin">
                        <a href="/admin/edit-policy.php?type=privacy" class="c-btn c-btn--outline c-dashboard-admin__button">
                            Edit Privacy Policy
                        </a>
                        <a href="/admin/edit-policy.php?type=terms" class="c-btn c-btn--outline c-dashboard-admin__button">
                            Edit Terms of Service
                        </a>
                        <a href="/admin/edit-policy.php?type=community" class="c-btn c-btn--outline c-dashboard-admin__button">
                            Edit Community Guidelines
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>
    <!-- Footer -->
    <?php require_once __DIR__ . '/partials/footer.php'; ?>
</body>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<!-- Custom JS -->
<script src="/assets/js/auth.js"></script>
<script src="/assets/js/cookie-banner.js"></script>
<script src="/assets/js/nav.js"></script>
<script src="/assets/js/footer.js"></script>
<script src="/assets/js/verse-modal.js"></script>
<script src="/assets/js/dashboard.js"></script>
</html>