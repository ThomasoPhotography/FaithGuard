<?php
/**
 * Session and user context for public pages that show the navigation bar.
 * Sets $is_logged_in, $user, $accountName and $user_role.
 */
require_once dirname(__DIR__) . '/db/config.php';

if (session_status() === PHP_SESSION_NONE) {
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
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once dirname(__DIR__) . '/db/database.php';
require_once dirname(__DIR__) . '/db/FaithGuardRepository.php';
require_once dirname(__DIR__) . '/api/helper/user.php';
require_once __DIR__ . '/seo.php';
require_once __DIR__ . '/rooms.php';
require_once __DIR__ . '/pages.php';

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user         = null;
$accountName  = 'Guest';
$user_role    = 'user';

if ($is_logged_in && isset($_SESSION['user_id'])) {
    try {
        $userData = FaithGuardRepository::getUserById($_SESSION['user_id']);
    } catch (Throwable $error) {
        error_log('FaithGuard page context: ' . $error->getMessage());
        $userData = null;
    }

    if ($userData) {
        $user        = normalize_user($userData);
        $accountName = $user['display_name'] ?? 'User';
        $user_role   = $user['role'] ?? 'user';
    } else {
        unset($_SESSION['user_id'], $_SESSION['logged_in']);
        $is_logged_in = false;
    }
}
