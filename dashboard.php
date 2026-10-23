<?php
    // Use centralized config for session cookie settings
    require_once __DIR__ . '/db/config.php';

    // Determine safe cookie domain (strip port, validate)
    $rawHost      = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
    $host         = preg_replace('/:.+$/', '', $rawHost);
    $cookieDomain = '';
    if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
    $cookieDomain = $host;
    }
    session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => '/',
    'domain'   => $cookieDomain,
    'secure'   => defined('SESSION_COOKIE_SECURE') ? SESSION_COOKIE_SECURE : true,
    'httponly' => defined('SESSION_COOKIE_HTTPONLY') ? SESSION_COOKIE_HTTPONLY : true,
    'samesite' => defined('SESSION_COOKIE_SAMESITE') ? SESSION_COOKIE_SAMESITE : 'Lax',
    ]);
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }
    // --- Core Site Requirements (Always required) ---
    require_once __DIR__ . '/db/FaithGuardRepository.php';
    require_once __DIR__ . '/api/helper/user.php';
    // --- Core Site Session Check (Always required) ---
    if (! isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
    }
    $is_logged_in   = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    $user           = FaithGuardRepository::getUserById($_SESSION['user_id']);
    $user           = normalize_user($user);
    $progress       = FaithGuardRepository::getUserProgress($_SESSION['user_id']);
    $checkins       = FaithGuardRepository::getUserCheckins($_SESSION['user_id'], 7);
    $lastQuizResult = FaithGuardRepository::getLastQuizResult($_SESSION['user_id']);
    // Prepare bible/book defaults to avoid undefined variable notices in template
    $books    = FaithGuardRepository::getAllBooks();
    $language = $_SESSION['language'] ?? 'en';
    $bookId   = $books[0]['id'] ?? null;
    if (! $user) {
    session_destroy();
    header('Location: /');
    exit;
    }
    $isAdmin = ! empty($user['is_admin']);
    // Redirect admins to admin dashboard
    if ($isAdmin) {
    header('Location: /admin/dashboard.php');
    exit;
    }
?>
<?php
    require_once __DIR__ . '/includes/seo.php';
    require_once __DIR__ . '/includes/rooms.php';
    require_once __DIR__ . '/includes/pages.php';
    $meta = fg_private_meta('Dashboard', '/dashboard.php');
    fg_render_head($meta);
?>
    <!-- Nav -->
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <!-- Dashboard Content -->
    <main id="main" class="c-dashboard">
        <div class="c-dashboard__container container">
            <header class="c-dashboard__header">
                <h1 class="c-dashboard__title"> Welcome, <?php echo htmlspecialchars($user['display_name'] ?? 'Friend'); ?>
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
                                <?php foreach ($books as $book): ?>
                                    <option value="<?php echo htmlspecialchars($book['id']); ?>">
                                        <?php echo htmlspecialchars($book['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="chapter" class="c-dashboard-scripture__select">
                                <?php $chapters = $bookId ? FaithGuardRepository::getBookChapters($bookId, $language) : []; ?>
                                <?php foreach ($chapters as $chapterRow): ?>
                                    <?php $chapterNum = $chapterRow['chapter_number'] ?? $chapterRow['chapter'] ?? $chapterRow; ?>
                                    <option value="<?php echo htmlspecialchars($chapterNum); ?>">
                                        <?php echo htmlspecialchars($chapterNum); ?>
                                    </option>
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
    </main>
    <!-- Footer -->
    <?php require __DIR__ . '/partials/footer.php'; ?>
<?php require __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>