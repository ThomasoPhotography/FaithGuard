<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

if (! ENABLE_REGISTRATION) {
    errorResponse('Registration is currently disabled', 403);
}

$input = getJsonInput();
validateRequired($input, ['first_name', 'last_name', 'email', 'password']);

$fullName  = trim($input['first_name'] . ' ' . $input['last_name']);
$email     = filter_var(sanitize($input['email']), FILTER_VALIDATE_EMAIL);
$password  = $input['password'];
$firstName = isset($input['first_name']) ? sanitize($input['first_name']) : null;
$lastName  = isset($input['last_name']) ? sanitize($input['last_name']) : null;

// Validate username
if (! preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
    errorResponse('Username must be 3-50 characters and contain only letters, numbers, and underscores');
}

// Validate email
if (! $email) {
    errorResponse('Invalid email address');
}

// Validate password
if (strlen($password) < PASSWORD_MIN_LENGTH) {
    errorResponse('Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters');
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
    'first_name'    => $firstName,
    'last_name'     => $lastName,
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
