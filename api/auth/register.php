<?php
require_once __DIR__ . '/../base.php';

session_start();
if (! empty($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
}
// If caller requests the registration modal (GET), return HTML fragment
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Return HTML for modal so front-end can inject it
        header('Content-Type: text/html; charset=utf-8');
        if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $csrf = htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
        echo <<<HTML
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Create account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="c-form__register">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First name</label>
                        <input id="first_name" name="first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last name</label>
                        <input id="last_name" name="last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" name="password" type="password" class="form-control" required>
                    </div>
                    <input type="hidden" name="csrf_token" value="{$csrf}">
                    <div class="c-form__message mb-3"></div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn c-btn c-btn--primary">Create account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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
validateRequired($input, ['first_name', 'last_name', 'email', 'password', 'csrf_token']);

// CSRF validation
if (! isset($input['csrf_token']) || ! isset($_SESSION['csrf_token']) || ! hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
        errorResponse('Invalid CSRF token', 403);
}

// Password hashing options
define('PASSWORD_ARGON2ID', 1);
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_MAX_LENGTH', 72);

// Input validation
$firstName = trim($input['first_name'] ?? '');
$lastName = trim($input['last_name'] ?? '');
$email = trim($input['email'] ?? '');
$password = password_hash($input['password'] ?? '', PASSWORD_ARGON2ID);
$fullName = $firstName . ' ' . $lastName;
// Validate username (first name portion)
if (! preg_match('/^[a-zA-Z0-9_]{3,50}$/', $firstName)) {
        errorResponse('Username must be 3-50 characters and contain only letters, numbers, and underscores');
}

// Validate email
if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        errorResponse('Invalid email address');
}

// Validate password length
if (strlen($password) < PASSWORD_MIN_LENGTH) {
        errorResponse('Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters');
}
if (strlen($password) > PASSWORD_MAX_LENGTH) {
        errorResponse('Password must be no more than ' . PASSWORD_MAX_LENGTH . ' characters');
}

// Check if email exists
if (FaithGuardRepository::getUserByEmail($email)) {
        errorResponse('Email is already registered');
}

// Create user
$userId = FaithGuardRepository::createUser([
        'first_name'    => $firstName,
        'last_name'     => $lastName,
        'full_name'     => $fullName,
        'email'         => $email,
        'password_hash' => $password,
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
        ],
]);