<?php
require_once __DIR__ . '/../db/database.php';
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../db/FaithGuardRepository.php';
require_once __DIR__ . '/../helper/CsrfTokenGenerator.php';

// Set headers for JSON API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

/**
 * Start the PHP session using the same cookie settings as API authentication.
 */
function startAppSession(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'secure'   => SESSION_COOKIE_SECURE,
        'httponly' => SESSION_COOKIE_HTTPONLY,
        'samesite' => SESSION_COOKIE_SAMESITE,
    ]);
    session_start();
}

// Handle preflight requests
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Send JSON response
 */
function jsonResponse(mixed $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

/**
 * Send success response
 */
function successResponse(array $data = []): void
{
    jsonResponse(['success' => true] + $data);
}

/**
 * Send error response
 */
function errorResponse(string $message, int $statusCode = 400): void
{
    jsonResponse(['success' => false, 'error' => $message], $statusCode);
}

/**
 * Get JSON input from request body
 */
function getJsonInput(): array
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

/**
 * Get current user from session
 */
function getCurrentUser(): ?array
{
    $sessionId = $_COOKIE[SESSION_COOKIE_NAME] ?? null;

    if (! $sessionId) {
        // Check Authorization header as fallback. getallheaders() is not
        // available with every PHP SAPI (for example PHP-FPM).
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

        if ($authHeader === '' && function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $name => $value) {
                if (strcasecmp($name, 'Authorization') === 0) {
                    $authHeader = $value;
                    break;
                }
            }
        }

        if (preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
            $sessionId = $matches[1];
        }
    }

    if (! $sessionId) {
        return null;
    }

    return FaithGuardRepository::getSession($sessionId);
}

/**
 * Require authentication
 */
function requireAuth(): array
{
    $user = getCurrentUser();
    if (! $user) {
        errorResponse('Authentication required', 401);
    }
    return $user;
}

/**
 * Require admin privileges
 */
function requireAdmin(): array
{
    $user = requireAuth();
    if (! $user['is_admin']) {
        errorResponse('Admin access required', 403);
    }
    return $user;
}

/**
 * Validate required fields
 */
function validateRequired(array $data, array $fields): void
{
    $missing = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $missing[] = $field;
        }
    }
    if (! empty($missing)) {
        errorResponse('Missing required fields: ' . implode(', ', $missing));
    }
}

/**
 * Extract a CSRF token from JSON, form data, or request headers.
 */
function getCsrfTokenFromRequest(array $input = []): string
{
    $candidates = [];

    if (! empty($input['csrf_token'])) {
        $candidates[] = $input['csrf_token'];
    }

    if (! empty($_POST['csrf_token'])) {
        $candidates[] = $_POST['csrf_token'];
    }

    foreach (['_SERVER' => ['HTTP_X_CSRF_TOKEN', 'HTTP_X_CSRF-TOKEN'], '_GET' => ['csrf_token']] as $source => $keys) {
        foreach ($keys as $key) {
            $value = $source === '_SERVER' ? ($_SERVER[$key] ?? '') : ($_GET[$key] ?? '');
            if (! empty($value)) {
                $candidates[] = $value;
            }
        }
    }

    foreach ($candidates as $candidate) {
        if (is_string($candidate)) {
            $token = trim($candidate);
            if ($token !== '') {
                return $token;
            }
        }
    }

    return '';
}

/**
 * Ensure the PHP session has a CSRF token and return it.
 */
function ensureCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token from request against database.
 */
function validateCsrfToken(): void
{
    $input = getJsonInput();
    $token = getCsrfTokenFromRequest($input);
    if (empty($token) || ! FaithGuardRepository::validateCsrfToken($token)) {
        errorResponse('Invalid or expired CSRF token', 403);
    }
}

/**
 * Sanitize string input
 */
function sanitize(string $input): string
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate secure random token
 */
function generateToken(int $length = 64): string
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Set session cookie
 */
function setSessionCookie(string $token, int $expiresAt): void
{
    setcookie(
        SESSION_COOKIE_NAME,
        $token,
        [
            'expires'  => $expiresAt,
            'path'     => '/',
            'secure'   => SESSION_COOKIE_SECURE,
            'httponly' => SESSION_COOKIE_HTTPONLY,
            'samesite' => SESSION_COOKIE_SAMESITE,
        ]
    );
}

/**
 * Clear session cookie
 */
function clearSessionCookie(): void
{
    setcookie(
        SESSION_COOKIE_NAME,
        '',
        [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => SESSION_COOKIE_SECURE,
            'httponly' => SESSION_COOKIE_HTTPONLY,
            'samesite' => SESSION_COOKIE_SAMESITE,
        ]
    );
}
