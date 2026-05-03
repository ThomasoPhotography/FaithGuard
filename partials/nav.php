<!-- Header -->
    <?php
        // Normalize variable names from different pages
        if (! isset($is_logged_in) && isset($isLoggedIn)) {
            $is_logged_in = $isLoggedIn;
        }
        if (! isset($user) && isset($currentUser)) {
            $user = $currentUser;
        }
        // Determine current page fallback
        $currentPage = isset($currentPage) ? $currentPage : basename($_SERVER['PHP_SELF'], '.php');
        // Display name for user (normalized helper adds display_name)
        $displayName = 'Guest';
        if (! empty($user) && is_array($user)) {
            $displayName = $user['display_name'] ?? $user['username'] ?? $user['email'] ?? 'User';
        }
        $isLoggedIn = ! empty($is_logged_in);

    ?>

    <header class="c-header">
        <div class="c-header__inner">
            <!-- Logo -->
            <a href="/" class="c-header__logo">
                <img src="/assets/images/logo.png" alt="FaithGuard Logo" class="c-header__logo--image">
            </a>

            <!-- Desktop Navigation -->
            <nav class="c-header__nav">
                <a href="/" class="c-header__nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">Home</a>
                <a href="/resources.php" class="c-header__nav-link <?php echo $currentPage === 'resources' ? 'active' : ''; ?>">Resources</a>
                <a href="/about.php" class="c-header__nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>">About</a>
                <a href="/contact.php" class="c-header__nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>">Contact</a>
            </nav>

            <!-- Actions -->
            <div class="c-header__actions">
                <?php if ($isLoggedIn && ! empty($user)): ?>
                    <a href="/dashboard.php" class="c-btn c-btn--ghost c-btn--sm">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <?php echo htmlspecialchars($displayName); ?>
                    </a>
                    <button class="c-btn c-btn--primary c-btn--sm" data-action="logout">
                        Logout
                    </button>
                <?php else: ?>
                    <a href="/login.php" class="c-btn c-btn--ghost c-btn--sm">Login</a>
                    <a href="/register.php" class="c-btn c-btn--primary c-btn--sm">Register</a>
                <?php endif; ?>

                <!-- Mobile Menu Button -->
                <button class="c-header__menu-c-btn" aria-label="Open menu" data-action="open-menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation -->
    <div class="c-mobile-nav" id="mobile-nav">
        <div class="c-mobile-nav__panel">
            <button class="c-mobile-nav__close" aria-label="Close menu" data-action="close-menu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"></path>
                </svg>
            </button>

            <nav class="c-mobile-nav__links">
                <a href="/" class="c-mobile-nav__link">Home</a>
                <a href="/resources.php" class="c-mobile-nav__link">Resources</a>
                <a href="/about.php" class="c-mobile-nav__link">About</a>
                <a href="/contact.php" class="c-mobile-nav__link">Contact</a>
                <?php if ($isLoggedIn): ?>
                    <a href="/dashboard.php" class="c-mobile-nav__link">Dashboard</a>
                <?php else: ?>
                    <a href="/login.php" class="c-mobile-nav__link">Login</a>
                    <a href="/register.php" class="c-mobile-nav__link">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>