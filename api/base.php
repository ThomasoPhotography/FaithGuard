<?php
require_once __DIR__ . '/../db/FaithGuardRepository.php';

// Set headers for JSON API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Send JSON response
 */
function jsonResponse($data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

/**
 * Send success response
 */
function successResponse(array $data = []): void {
    jsonResponse(['success' => true] + $data);
}

/**
 * Send error response
 */
function errorResponse(string $message, int $statusCode = 400): void {
    jsonResponse(['success' => false, 'error' => $message], $statusCode);
}

/**
 * Get JSON input from request body
 */
function getJsonInput(): array {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

/**
 * Get current user from session
 */
function getCurrentUser(): ?array {
    $sessionId = $_COOKIE[SESSION_COOKIE_NAME] ?? null;
    
    if (!$sessionId) {
        // Check Authorization header as fallback
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        if (preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
            $sessionId = $matches[1];
        }
    }
    
    if (!$sessionId) {
        return null;
    }
    
    return FaithGuardRepository::getSession($sessionId);
}

/**
 * Require authentication
 */
function requireAuth(): array {
    $user = getCurrentUser();
    if (!$user) {
        errorResponse('Authentication required', 401);
    }
    return $user;
}

/**
 * Require admin privileges
 */
function requireAdmin(): array {
    $user = requireAuth();
    if (!$user['is_admin']) {
        errorResponse('Admin access required', 403);
    }
    return $user;
}

/**
 * Validate required fields
 */
function validateRequired(array $data, array $fields): void {
    $missing = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $missing[] = $field;
        }
    }
    if (!empty($missing)) {
        errorResponse('Missing required fields: ' . implode(', ', $missing));
    }
}

/**
 * Sanitize string input
 */
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate secure random token
 */
function generateToken(int $length = 64): string {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Set session cookie
 */
function setSessionCookie(string $token, int $expiresAt): void {
    setcookie(
        SESSION_COOKIE_NAME,
        $token,
        [
            'expires' => $expiresAt,
            'path' => '/',
            'secure' => SESSION_COOKIE_SECURE,
            'httponly' => SESSION_COOKIE_HTTPONLY,
            'samesite' => SESSION_COOKIE_SAMESITE
        ]
    );
}

/**
 * Clear session cookie
 */
function clearSessionCookie(): void {
    setcookie(
        SESSION_COOKIE_NAME,
        '',
        [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => SESSION_COOKIE_SECURE,
            'httponly' => SESSION_COOKIE_HTTPONLY,
            'samesite' => SESSION_COOKIE_SAMESITE
        ]
    );
}