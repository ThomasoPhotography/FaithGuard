<?php
declare (strict_types = 1);
// FaithGuard Configuration File
// This file contains sensitive settings. Do NOT commit to version control.
// For production, use environment variables instead of hardcoding values.

// Bible API Settings (from api.bible)
define('BIBLE_API_KEY', 'VrnmMFY2YCN4xcBPUnzxf');
define('BIBLE_API_BASE_URL', 'https://api.bible/v1');

// Example: App Settings
define('SITE_NAME', 'FaithGuard');
define('SITE_VERSION', '0.1.4');
define('SITE_ENV', 'development'); // Change to 'production' in live

// Example: Session Settings (already in individual files, but centralized here if needed)
define('SESSION_LIFETIME', 302400); // 3.5 days

// Prevent direct access
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    die('Direct access not allowed.');
}

// Language configuration
const SITE_LANGUAGES     = ['en', 'nl'];
const DEFAULT_LANGUAGE   = 'en';
const BIBLE_TRANSLATIONS = [
    'en' => 'NRSVUE',
    'nl' => 'NBV21',
];
