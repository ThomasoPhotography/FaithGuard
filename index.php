<?php
    // Use centralized config for session cookie settings when available
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

    // --- Core App Requirements (Always required) ---
    require_once __DIR__ . '/db/database.php';
    require_once __DIR__ . '/db/FaithGuardRepository.php';
    // --- Optional Helper/Debug (Required, but note its function) ---
    require_once __DIR__ . '/api/helper/debug.php';
    // User normalization helper
    require_once __DIR__ . '/api/helper/user.php';

    $is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

    // --- CRITICAL FIX: Define user variables needed for navigation bar ---
    $user        = null;
    $accountName = 'Guest';
    $user_role   = 'user';

    if ($is_logged_in && isset($_SESSION['user_id'])) {
    // Fetch user data using the repository method
    $user_data = FaithGuardRepository::getUserById($_SESSION['user_id']);

    if ($user_data) {
        // Normalize user data for templates
        $user        = normalize_user($user_data);
        $accountName = $user['display_name'] ?? 'User';
        $user_role   = $user['role'] ?? 'user';
    } else {
        // Logged-in session exists, but user not found in DB (session cleanup needed)
        unset($_SESSION['user_id']);
        unset($_SESSION['logged_in']);
        $is_logged_in = false;
    }
    }

                            // --- Fetch Resources for Dynamic Display ---
                            // Fetch resources from DB. Provide explicit defaults to avoid undefined variable notices.
    $category           = null; // e.g. 'video', 'article'
    $max_resources      = 6;    // Limit to 6 for display (adjust as needed)
    $featured_resources = FaithGuardRepository::getFeaturedResources($max_resources);
    $resources          = FaithGuardRepository::getResources();

                                                          // --- Testimonials (Community Impact) ---
    $testimonials = FaithGuardRepository::getTestimonials(3); // Fetch 3 testimonials for display
?>
<?php
    require_once __DIR__ . '/includes/seo.php';
    require_once __DIR__ . '/includes/rooms.php';
    require_once __DIR__ . '/includes/pages.php';
    $meta = fg_page_meta('home');
    fg_render_head($meta);
?>
<?php require __DIR__ . '/partials/nav.php'; ?>
    <!-- Hero Content -->
    <header class="c-hero">
        <div class="container-fluid">
            <div class="c-hero__content text-center">
                <h1 class="c-hero__title">Take back your time online with Scripture, prayer and community</h1>
                <p class="c-hero__text">Free, vetted resources to support your journey toward spiritual freedom. Rooted in Christian hope and redemption.</p>
                <a href="quiz.php" class="btn c-btn c-hero__btn">Take the Quiz</a>
            </div>
        </div>
    </header>
    <!-- Main Content -->
    <main id="main" class="c-main container my-5">
    <!-- The eight rooms -->
    <section class="c-main__section c-rooms mb-5" id="rooms" aria-labelledby="rooms-title">
    <div class="c-rooms__header">
    <div class="c-rooms__intro">
    <h2 class="c-main__title" id="rooms-title">Scripture is the doorway; eight rooms behind it.</h2>
    <p class="c-main__text">Read by walking in, lived by walking further. Each doorway opens onto the same home.</p>
    </div>
    <a class="btn c-btn c-btn__outline" href="/contact.php">Ask about a room</a>
    </div>
    <ul class="c-rooms__grid">
    <?php foreach (rooms() as $room): ?>
    <li class="c-rooms__item<?php echo $room['slug'] === 'scripture' ? ' c-rooms__item--featured' : ''; ?>">
    <a class="c-room-card<?php echo $room['slug'] === 'scripture' ? ' c-room-card--featured' : ''; ?>" href="/rooms/<?php echo fg_e($room['slug']); ?>">
    <h3 class="c-room-card__name"><?php echo fg_e($room['name']); ?></h3>
    <p class="c-room-card__short"><?php echo fg_e($room['short']); ?></p>
    </a>
    </li>
    <?php endforeach; ?>
    </ul>
    </section>
    <!-- Resource Section -->
        <section class="c-main__section mb-5">
            <h2 class="c-main__title">Our Mission</h2>
            <p class="c-main__text">FaithGuard provides resources and tools to help people regain and protect their faith from digital addictions, including pornography. Read <a href="/about.php">how FaithGuard approaches recovery</a>.</p>
        </section>
        <!-- Featured Resources Section -->
        <section class="c-main__section mb-5">
            <h2 class="c-main__title">Featured Resources</h2>
            <div class="row c-resources__list">
                <?php if (! empty($featured_resources)): ?>
                    <?php foreach ($featured_resources as $resource): ?>
                        <?php $excerpt = $resource['content'] ?: ($resource['description'] ?? ''); ?>
                        <div class="col-md-5 col-12 col-lg-5 mb-4">
                            <div class="card c-card">
                                <div class="card-body c-card__body">
                                    <h5 class="card-title c-card__title"><?php echo htmlspecialchars($resource['title']); ?></h5>
                                    <p class="card-text c-card__text"><?php echo htmlspecialchars(substr($excerpt, 0, 100)) . (strlen($excerpt) > 100 ? '...' : ''); ?></p>
                                    <a href="/resources.php?slug=<?php echo rawurlencode((string) ($resource['slug'] ?? '')); ?>" class="btn c-btn c-card__btn">Learn More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-5 col-12 col-lg-5 mb-4">
                        <p class="text-center">No featured resources available yet. Check back soon!</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <!-- Call to Action Section -->
        <section class="c-main__section text-center mb-5">
            <h2 class="c-main__titlepop">Join the FaithGuard Community</h2>
            <p class="c-main__text">Sign up today to access personalized resources, track your progress, and connect with others on the same journey.</p>
            <a href="#" role="button" onclick="openRegisterModal(); return false;" class="btn c-btn c-btn__outline">Get Started</a>
            <a href="#" role="button" class="c-main__text c-main__text--verse" data-action="open-verse-modal">2 Corinthians 5:7</a>
        </section>
    </main>
    <?php if (! empty($testimonials)): ?>
    <!-- Community Impact Section -->
    <article class="container-fluid mb-4">
        <section class="c-main__section c-impact mb-5">
            <h2 class="c-impact__title text-center">Community Impact</h2>
            <p class="c-impact__intro text-center">Hear from those who have found hope and strength through FaithGuard's faith-based resources.</p>
            <div class="row">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="col-md-5 col-12 col-lg-4 mb-4">
                        <div class="card c-card c-impact__card">
                            <div class="card-body c-card__body c-impact__body">
                                <blockquote class="c-impact__quote"><?php echo htmlspecialchars($testimonial['testimonial']); ?></blockquote>
                                <cite class="c-impact__cite">- <?php echo htmlspecialchars($testimonial['first_name'] ?? 'Anonymous'); ?></cite>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </article>
    <?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
<?php require __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>