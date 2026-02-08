<?php
/**
 * FaithGuard Configuration
 * 
 * Central configuration file for database and API credentials.
 * This file should be kept secure and not exposed to public access.
 */

// Prevent direct access
if (!defined('FAITHGUARD_ROOT')) {
    define('FAITHGUARD_ROOT', dirname(__DIR__));
}

// Database Configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'faithguard');
define('DB_USER', $_ENV['DB_USER'] ?? 'faithguard_user');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'your_secure_password_here');
define('DB_CHARSET', 'utf8mb4');

// API.Bible Configuration
// API Key for scripture access - NRSVUE (English) and NBV21 (Dutch)
define('BIBLE_API_KEY', $_ENV['BIBLE_API_KEY'] ?? 'jhCTF0KSddJvqBAb-na7p');
define('BIBLE_API_BASE_URL', 'https://api.scripture.api.bible/v1');

// Bible Version IDs
// NRSVUE: New Revised Standard Version Updated Edition (English)
// NLD1939: Statenvertaling (Dutch)
// NBV21: Nederlandse Bijbelvertaling 2021 (Dutch) when approved by NBG
define('BIBLE_VERSION_EN', 'NRSVUE');
define('BIBLE_VERSION_ID_EN', '9879dbb7cfe39e4d-01');
//define('BIBLE_VERSION_NL', 'NBV21');
//define('BIBLE_VERSION_ID_NL', 'ad7a5f55c8c8b00d-01');
define('BIBLE_VERSION_NL', 'NLD1939');
define('BIBLE_VERSION_ID_NL', 'ead7b4cc5007389c-01');

// Application Settings
define('APP_NAME', 'FaithGuard');
define('APP_URL', $_ENV['APP_URL'] ?? 'https://faithguard.site');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', $_ENV['APP_DEBUG'] ?? false);

// Session Configuration
define('SESSION_LIFETIME', 60 * 60 * 24 * 7); // 7 days
define('SESSION_COOKIE_NAME', 'fg_session');
define('SESSION_COOKIE_SECURE', true);
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_COOKIE_SAMESITE', 'Strict');

// Security Settings
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Feature Flags
define('ENABLE_REGISTRATION', true);
define('ENABLE_PRAYER_REQUESTS', true);
define('ENABLE_COMMUNITY_POSTS', true);
define('ENABLE_QUIZ', true);

// Error reporting (disable in production)
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Timezone
date_default_timezone_set('UTC');
