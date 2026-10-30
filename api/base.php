
<?php

    declare (strict_types = 1);

    /*
|--------------------------------------------------------------------------
| Preflight requests
|--------------------------------------------------------------------------
*/

    if (
    isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD'] === 'OPTIONS'
    ) {
    http_response_code(204);
    exit;
    }

    /*
|--------------------------------------------------------------------------
| Error reporting
|--------------------------------------------------------------------------
|
| Do not expose PHP errors in production.
| Log errors instead.
|
*/

    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');

    error_reporting(E_ALL);

    /*
|--------------------------------------------------------------------------
| Dependencies
|--------------------------------------------------------------------------
*/

    require_once __DIR__ . '/../db/config.php';
    require_once __DIR__ . '/../db/database.php';
    require_once __DIR__ . '/../db/FaithGuardRepository.php';
    require_once __DIR__ . '/helper/CsrfTokenGenerator.php';

    /*
|--------------------------------------------------------------------------
| Security headers
|--------------------------------------------------------------------------
*/

    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    /*
|--------------------------------------------------------------------------
| CORS configuration
|--------------------------------------------------------------------------
|
| Restrict API access to your own website.
| Never combine wildcard origins with credentials.
|
*/

    $allowedOrigins = [
    'https://faithguard.site',
    'https://www.faithguard.site',
    ];

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if ($origin !== '') {
    if (! in_array($origin, $allowedOrigins, true)) {
        errorResponse('Origin not allowed', 403);
    }

    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Vary: Origin');
    }

    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token');

    /*
|--------------------------------------------------------------------------
| PHP session management
|--------------------------------------------------------------------------
*/

    /**
 * Start the application session.
 */
    function startAppSession(): void
    {
    if (session_status() === PHP_SESSION_DISABLED) {
        throw new RuntimeException('PHP sessions are disabled.');
    }

    if (session_status() === PHP_SESSION_NONE) {

        if (headers_sent($file, $line)) {
            throw new RuntimeException(
                "Headers already sent in {$file} on line {$line}."
            );
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

    ensureCsrfToken();
    }

    /*
|--------------------------------------------------------------------------
| CSRF protection
|--------------------------------------------------------------------------
*/

    /**
 * Generate or retrieve the current session CSRF token.
 */
    function ensureCsrfToken(): string
    {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new RuntimeException(
            'An active session is required for CSRF protection.'
        );
    }

    if (
        empty($_SESSION['csrf_token']) ||
        ! is_string($_SESSION['csrf_token'])
    ) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
    }

    /**
 * Extract a CSRF token from the request.
 */
    function getCsrfTokenFromRequest(array $input = []): string
    {
    $candidates = [
        $input['csrf_token'] ?? null,
        $_POST['csrf_token'] ?? null,
        $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null,
        $_GET['csrf_token'] ?? null,
    ];

    foreach ($candidates as $candidate) {
        if (is_string($candidate) && trim($candidate) !== '') {
            return trim($candidate);
        }
    }

    return '';
    }

    /**
 * Validate the submitted token against the active PHP session.
 *
 * Use this for session-based authentication endpoints.
 */
    function validateSessionCsrfToken(?array $input = null): void
    {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        errorResponse('Session not initialised', 403);
    }

    $input ??= getJsonInput();

    $submittedToken = getCsrfTokenFromRequest($input);
    $sessionToken   = $_SESSION['csrf_token'] ?? '';

    if (
        ! is_string($sessionToken) ||
        ! is_string($submittedToken) ||
        $sessionToken === '' ||
        $submittedToken === '' ||
        ! hash_equals($sessionToken, $submittedToken)
    ) {
        errorResponse('Invalid or expired CSRF token', 403);
    }
    }

    /**
 * Validate a database-backed CSRF token.
 *
 * Retained for existing database-backed CSRF workflows.
 */
    function validateCsrfToken(): void
    {
    $input = getJsonInput();
    $token = getCsrfTokenFromRequest($input);

    if (
        $token === '' ||
        ! FaithGuardRepository::validateCsrfToken($token)
    ) {
        errorResponse('Invalid or expired CSRF token', 403);
    }
    }

    /*
|--------------------------------------------------------------------------
| JSON responses
|--------------------------------------------------------------------------
*/

    /**
 * Send a JSON response and terminate execution.
 */
    function jsonResponse(mixed $data, int $statusCode = 200): void
    {
    http_response_code($statusCode);

    try {
        $json = json_encode(
            $data,
            JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE |
            JSON_INVALID_UTF8_SUBSTITUTE |
            JSON_THROW_ON_ERROR
        );

        echo $json;
    } catch (JsonException $exception) {
        http_response_code(500);

        echo '{"success":false,"error":"Unable to generate response."}';
    }

    exit;
    }

    /**
 * Send a successful JSON response.
 */
    function successResponse(array $data = []): void
    {
    jsonResponse(['success' => true] + $data);
    }

    /**
 * Send an error JSON response.
 */
    function errorResponse(string $message, int $statusCode = 400): void
    {
    jsonResponse([
        'success' => false,
        'error'   => $message,
    ], $statusCode);
    }

    /*
|--------------------------------------------------------------------------
| Request input
|--------------------------------------------------------------------------
*/
    /**
 * Retrieve and decode JSON request input.
 */
    function getJsonInput(): array
    {
    $input = file_get_contents('php://input');
    if ($input === false || trim($input) === '') {
        return [];
    }
    try {
        $decoded = json_decode(
            $input,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        return is_array($decoded) ? $decoded : [];
    } catch (JsonException $exception) {
        errorResponse('Invalid JSON request body', 400);
        return [];
    }
    }

    /**
 * Validate required request fields.
 */
    function validateRequired(array $data, array $fields): void
    {
    $missing = [];

    foreach ($fields as $field) {
        if (
            ! isset($data[$field]) ||
            (is_string($data[$field]) && trim($data[$field]) === '')
        ) {
            $missing[] = $field;
        }
    }

    if (! empty($missing)) {
        errorResponse(
            'Missing required fields: ' . implode(', ', $missing),
            400
        );
    }
    }

    /*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

    /**
 * Retrieve the currently authenticated user.
 */
    function getCurrentUser(): ?array
    {
    $sessionId = $_COOKIE[SESSION_COOKIE_NAME] ?? null;

    if (! $sessionId) {
        $authHeader =
        $_SERVER['HTTP_AUTHORIZATION'] ??
        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ??
            '';

        if ($authHeader === '' && function_exists('getallheaders')) {
            $headers = getallheaders();

            foreach ($headers as $name => $value) {
                if (strcasecmp($name, 'Authorization') === 0) {
                    $authHeader = $value;
                    break;
                }
            }
        }

        if (
            is_string($authHeader) &&
            preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $matches)
        ) {
            $sessionId = $matches[1];
        }
    }

    if (
        ! is_string($sessionId) ||
        $sessionId === ''
    ) {
        return null;
    }

    return FaithGuardRepository::getSession($sessionId);
    }

    /**
 * Require an authenticated user.
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
 * Require administrator privileges.
 */
    function requireAdmin(): array
    {
    $user = requireAuth();

    if (empty($user['is_admin'])) {
        errorResponse('Admin access required', 403);
    }

    return $user;
    }

    /*
|--------------------------------------------------------------------------
| Input sanitisation
|--------------------------------------------------------------------------
*/

    /**
 * Sanitize a string for HTML output.
 *
 * Prefer context-specific escaping when rendering output.
 */
    function sanitize(string $input): string
    {
    return htmlspecialchars(
        strip_tags(trim($input)),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
    }

    /*
|--------------------------------------------------------------------------
| Token generation
|--------------------------------------------------------------------------
*/

    /**
 * Generate a cryptographically secure hexadecimal token.
 */
    function generateToken(int $length = 64): string
    {
    if ($length < 2 || $length % 2 !== 0) {
        throw new InvalidArgumentException(
            'Token length must be an even number greater than zero.'
        );
    }

    return bin2hex(random_bytes(intdiv($length, 2)));
    }

    /*
|--------------------------------------------------------------------------
| Authentication session cookies
|--------------------------------------------------------------------------
*/

    /**
 * Set the authentication session cookie.
 */
    function setSessionCookie(string $token, int $expiresAt): void
    {
    if (headers_sent()) {
        throw new RuntimeException(
            'Cannot set session cookie: headers already sent.'
        );
    }

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
 * Clear the authentication session cookie.
 */
    function clearSessionCookie(): void
    {
    if (headers_sent()) {
        throw new RuntimeException(
            'Cannot clear session cookie: headers already sent.'
        );
    }

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
