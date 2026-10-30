<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../base.php';
require_once __DIR__ . '/../rate_limiter.php';

startAppSession();

if (! defined('PASSWORD_MIN_LENGTH')) {
    define('PASSWORD_MIN_LENGTH', 8);
}

if (! defined('PASSWORD_MAX_LENGTH')) {
    define('PASSWORD_MAX_LENGTH', 72);
}

/*
|--------------------------------------------------------------------------
| GET - Load registration modal
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: text/html; charset=utf-8');

    // Use session-based CSRF for guests
    $csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');

    echo <<<HTML
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="registerForm" action="/api/auth/register.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Create account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="c-form__message mb-3"></div>

                    <div class="mb-3">
                        <label for="first_name">First name</label>
                        <input class="form-control" id="first_name" name="first_name" autocomplete="given-name" required>
                    </div>

                    <div class="mb-3">
                        <label for="last_name">Last name</label>
                        <input class="form-control" id="last_name" name="last_name" autocomplete="family-name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" autocomplete="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" required>
                    </div>

                    <input type="hidden" name="csrf_token" value="$csrf">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create account</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
HTML;
    exit;
}

/*
|--------------------------------------------------------------------------
| POST ONLY
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

try {
    if (! ENABLE_REGISTRATION) {
        errorResponse('Registration disabled', 403);
    }

    $body = file_get_contents("php://input");
    $data = json_decode($body, true);

    if (! is_array($data)) {
        errorResponse('Invalid JSON', 400);
    }

    validateRequired(
        $data,
        ['first_name', 'last_name', 'email', 'password', 'csrf_token']
    );

/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
*/

    // Validate against PHP session token instead of the database
    if (empty($_SESSION['csrf_token']) || empty($data['csrf_token']) || ! hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
        errorResponse('Invalid or expired CSRF token', 403);
    }

/*
|--------------------------------------------------------------------------
| Validate input
|--------------------------------------------------------------------------
*/

    $firstName = trim($data['first_name']);
    $lastName  = trim($data['last_name']);
    $email     = trim($data['email']);
    $password  = $data['password'];

    if (mb_strlen($firstName) < 2 || mb_strlen($firstName) > 50) {
        errorResponse('Invalid first name');
    }

    if (mb_strlen($lastName) < 2 || mb_strlen($lastName) > 50) {
        errorResponse('Invalid last name');
    }

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        errorResponse('Invalid email');
    }

    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        errorResponse('Password too short');
    }

/*
|--------------------------------------------------------------------------
| Existing account
|--------------------------------------------------------------------------
*/

    if (FaithGuardRepository::getUserByEmail($email)) {
        errorResponse('Email already registered');
    }

/*
|--------------------------------------------------------------------------
| Create account
|--------------------------------------------------------------------------
*/

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $userId = FaithGuardRepository::createUser([
        'first_name'    => $firstName,
        'last_name'     => $lastName,
        'email'         => $email,
        'password_hash' => $passwordHash,
    ]);

    if (! $userId) {
        errorResponse('Could not create user', 500);
    }

/*
|--------------------------------------------------------------------------
| Login session
|--------------------------------------------------------------------------
*/

    $sessionToken = bin2hex(random_bytes(32));
    $expires      = gmdate('Y-m-d H:i:s', time() + SESSION_LIFETIME);

    FaithGuardRepository::createSession(
        $sessionToken,
        $userId,
        $expires
    );

    setSessionCookie($sessionToken, strtotime($expires));

    $_SESSION['user_id']   = $userId;
    $_SESSION['logged_in'] = true;

    // Regenerate session ID and create a fresh authenticated CSRF token
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    successResponse([
        'user' => [
            'id'         => $userId,
            'email'      => $email,
            'first_name' => $firstName,
            'last_name'  => $lastName,
        ],
    ]);

} catch (Throwable $e) {
    errorResponse('System Crash: ' . $e->getMessage(), 500);
}
