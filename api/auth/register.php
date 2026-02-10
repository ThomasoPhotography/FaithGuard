<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

// Start session and ensure CSRF token exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    if (! isset($_SESSION['user_id'])) {
        session_regenerate_id(true);
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

if (! ENABLE_REGISTRATION) {
    errorResponse('Registration is currently disabled', 403);
}

$input = getJsonInput();
validateRequired($input, ['full_name', 'email', 'password', 'csrf_token']);

// CSRF validation
if (! isset($input['csrf_token']) || ! isset($_SESSION['csrf_token']) || ! hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
    errorResponse('Invalid CSRF token', 403);
}

$fullName = trim($input['full_name'] ?? '');
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
// Split full name into first and last name
$nameParts = explode(' ', $fullName, 2);
$firstName = $nameParts[0];
$lastName  = isset($nameParts[1]) ? $nameParts[1] : '';

// Validate username
if (! preg_match('/^[a-zA-Z0-9_]{3,50}$/', $firstName)) {
    errorResponse('Username must be 3-50 characters and contain only letters, numbers, and underscores');
}

// Validate email
if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
    errorResponse('Invalid email address');
}

// Validate password length (bcrypt/argon limits)
if (strlen($password) < PASSWORD_MIN_LENGTH) {
    errorResponse('Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters');
}
if (strlen($password) > 72) {
    errorResponse('Password must be no more than 72 characters');
}

// Check if email exists
if (FaithGuardRepository::getUserByEmail($email)) {
    errorResponse('Email is already registered');
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_ARGON2ID);

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
    ],
]);
