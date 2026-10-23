<?php
    declare (strict_types = 1);

    /** Expects $meta, $is_logged_in, $user, $accountName and $user_role from the including page. */
    $is_logged_in = $is_logged_in ?? false;
    $user         = $user ?? null;
    $accountName  = $accountName ?? 'Guest';
    $user_role    = $user_role ?? 'user';
    if ($is_logged_in && is_array($user)) {
    $accountName = (string) ($user['display_name'] ?? $accountName);
    $user_role   = (string) ($user['role'] ?? $user_role);
    }
    $profileUrl = $user_role === 'admin' ? '/admin/dashboard.php' : '/dashboard.php';
    if (! isset($meta) || ! is_array($meta)) {
    $meta = [];
    }
?>
<nav class="navbar navbar-expand-lg navbar-light c-nav" aria-label="Main">
    <div class="container-fluid">
        <a class="navbar-brand c-nav__brand" href="/">
            <img src="/assets/uploads/FaithGuard_Primary_Logo.svg" alt="FaithGuard logo" class="c-nav__logo" width="40" height="40">
            <span class="visually-hidden">FaithGuard, home</span>
        </a>
        <button class="navbar-toggler c-nav__toggler c-nav__toggler--btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse c-nav__collapse" id="navbarNav">
            <ul class="navbar-nav c-nav__bar me-auto">
                <li class="nav-item c-nav__item">
                    <a class="nav-link c-nav__link" href="/rooms/scripture"<?php echo fg_aria_current('/rooms/scripture', $meta) ?>>The Word</a>
                </li>
                <li class="nav-item dropdown c-nav__item">
                    <a class="nav-link dropdown-toggle c-nav__link" href="#" id="ecosystemDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">The Ecosystem</a>
                    <ul class="dropdown-menu c-dropdown__menu" aria-labelledby="ecosystemDropdown">
<?php foreach (rooms() as $room): ?>
                        <li><a class="dropdown-item c-dropdown__item" href="/rooms/<?php echo fg_e($room['slug']) ?>"<?php echo fg_aria_current('/rooms/' . $room['slug'], $meta) ?>><?php echo fg_e($room['name']) ?></a></li>
<?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item c-nav__item">
                    <a class="nav-link c-nav__link" href="/resources.php"<?php echo fg_aria_current('/resources.php', $meta) ?>>Resources</a>
                </li>
                <li class="nav-item c-nav__item">
                    <a class="nav-link c-nav__link" href="/about.php"<?php echo fg_aria_current('/about.php', $meta) ?>>A Quieter Way</a>
                </li>
                <li class="nav-item c-nav__item">
                    <a class="nav-link c-nav__link" href="/contact.php"<?php echo fg_aria_current('/contact.php', $meta) ?>>Reach the Hearth</a>
                </li>
            </ul>
<?php if ($is_logged_in && $user): ?>
            <div class="d-flex dropdown c-nav__dropdown c-dropdown">
                <button class="btn c-btn c-dropdown__btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="c-dropdown__icon bi bi-person-check me-1" aria-hidden="true"></i>
                    <span class="c-dropdown__text">Welcome <?php echo fg_e($accountName) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu" aria-labelledby="userDropdown">
                    <li><h6 class="dropdown-header c-dropdown__header">Signed in as: <?php echo fg_e(ucfirst($user_role)) ?></h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item c-dropdown__item" href="<?php echo fg_e($profileUrl) ?>">
                            <i class="bi bi-person-badge me-2" aria-hidden="true"></i>
                            <span class="c-dropdown__text">Dashboard</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item c-dropdown__item js-logout-btn" href="#">
                            <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>
                            <span class="c-dropdown__text">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
<?php else: ?>
            <div class="d-flex dropdown c-nav__dropdown c-dropdown">
                <button class="btn c-btn c-dropdown__btn js-dropdown-btn dropdown-toggle" type="button" id="loginDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="c-dropdown__icon bi bi-person-circle me-1" aria-hidden="true"></i>
                    <span class="c-dropdown__text">Login / Register</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end c-dropdown__menu js-dropdown-menu" aria-labelledby="loginDropdown">
                    <li><h6 class="dropdown-header c-dropdown__header">Sign Up / Log In</h6></li>
                    <li>
                        <label class="visually-hidden" for="signupUsername">Email</label>
                        <input type="email" id="signupUsername" class="form-control c-dropdown__info mb-2" placeholder="Email" autocomplete="email">
                    </li>
                    <li>
                        <label class="visually-hidden" for="signupPassword">Password</label>
                        <input type="password" id="signupPassword" class="form-control c-dropdown__info mb-2" placeholder="Password" autocomplete="current-password">
                    </li>
                    <li>
                        <a class="dropdown-item c-dropdown__item js-log mb-2" href="#">
                            <i class="bi bi-person-check me-2" aria-hidden="true"></i>
                            <span class="c-dropdown__text">Login</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item c-dropdown__item js-create" href="#" onclick="openRegisterModal(); return false;">
                            <i class="bi bi-person-plus me-2" aria-hidden="true"></i>
                            <span class="c-dropdown__text">Create Account</span>
                        </a>
                    </li>
                </ul>
            </div>
<?php endif; ?>
        </div>
    </div>
</nav>
<div id="modal-container"></div>
<?php fg_render_breadcrumbs($meta); ?>