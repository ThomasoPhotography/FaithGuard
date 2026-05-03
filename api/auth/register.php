<?php
require_once __DIR__ . '/../base.php';

session_start();
if (! empty($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
}
// Ensure password length constants exist (fall back to sensible defaults)
if (! defined('PASSWORD_MIN_LENGTH')) {
    define('PASSWORD_MIN_LENGTH', 8);
}
if (! defined('PASSWORD_MAX_LENGTH')) {
    define('PASSWORD_MAX_LENGTH', 72);
}
if (! defined('PASSWORD_ARGON2ID')) {
    // Use PHP's built-in constant if available, otherwise fallback to PASSWORD_DEFAULT
    if (defined('PASSWORD_ARGON2ID')) {
        // nothing
    } else {
        define('PASSWORD_ARGON2ID', PASSWORD_DEFAULT);
    }
}
// If caller requests the registration modal (GET), return HTML fragment
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return HTML for modal so front-end can inject it
    header('Content-Type: text/html; charset=utf-8');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf = htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');

    // Emit a Bootstrap modal fragment and include the register.js bootstrapper
    echo <<<HTML
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="registerForm" class="c-form__register">
                <div class="modal-header">
                    <h5 class="modal-title">Create account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
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
<script src="../../assets/js/register.js"></script>
<script>
    (function(){
        var modalEl = document.getElementById('registerModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    })();
</script>
HTML;

    exit;
}

// Only accept POST for registration submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

// Start session and ensure CSRF token exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

if (! ENABLE_REGISTRATION) {
    errorResponse('Registration is currently disabled', 403);
}

$input = getJsonInput();
// Accept full_name (single input) or first_name/last_name; require full_name for modal
validateRequired($input, ['full_name', 'email', 'password', 'csrf_token']);

// CSRF validation
if (! isset($input['csrf_token']) || ! isset($_SESSION['csrf_token']) || ! hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
    errorResponse('Invalid CSRF token', 403);
}

// Input validation
$fullName    = trim($input['full_name'] ?? '');
$email       = trim($input['email'] ?? '');
$rawPassword = $input['password'] ?? '';
// Split full name into first/last
$nameParts = explode(' ', $fullName, 2);
$firstName = $nameParts[0] ?? '';
$lastName  = $nameParts[1] ?? '';

// Validate username (first name portion)
if (! preg_match('/^[a-zA-Z0-9_]{3,50}$/', $firstName)) {
    errorResponse('Username must be 3-50 characters and contain only letters, numbers, and underscores');
}

// Validate email
if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
    errorResponse('Invalid email address');
}

// Validate password length
if (strlen($rawPassword) < PASSWORD_MIN_LENGTH) {
    errorResponse('Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters');
}
if (strlen($rawPassword) > PASSWORD_MAX_LENGTH) {
    errorResponse('Password must be no more than ' . PASSWORD_MAX_LENGTH . ' characters');
}

// Check if email exists
if (FaithGuardRepository::getUserByEmail($email)) {
    errorResponse('Email is already registered');
}

// Hash the password now
$passwordHash = password_hash($rawPassword, PASSWORD_ARGON2ID);

// Create user
$userId = FaithGuardRepository::createUser([
    'first_name'    => $firstName,
    'last_name'     => $lastName,
    'full_name'     => $fullName,
    'email'         => $email,
    'password_hash' => $passwordHash,
]);

if (! $userId) {
    errorResponse('Failed to create account. Please try again.', 500);
}

// Create session
$expiresAt    = time() + SESSION_LIFETIME;
$sessionToken = generateToken();

FaithGuardRepository::createSession($sessionToken, $userId, $expiresAt);
setSessionCookie($sessionToken, $expiresAt);

// Return success
successResponse([
    'user' => [
        'id'         => $userId,
        'email'      => $email,
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'full_name'  => $fullName,
        'is_admin'   => false,
        'is_member'  => false,
    ],
]);
