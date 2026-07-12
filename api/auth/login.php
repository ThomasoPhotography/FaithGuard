<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

startAppSession();

$input = getJsonInput();
validateRequired($input, ['email', 'password']);

$email = filter_var(sanitize($input['email']), FILTER_VALIDATE_EMAIL);
if (! $email) {
    errorResponse('Invalid email address');
}

$password = $input['password'];
$remember = $input['remember'] ?? false;

// Find user
$user = FaithGuardRepository::getUserByEmail($email);
if (! $user) {
    errorResponse('Invalid email or password', 401);
}

// Verify password
if (! password_verify($password, $user['password_hash'])) {
    errorResponse('Invalid email or password', 401);
}

// Update last login
FaithGuardRepository::updateLastLogin($user['id']);

// Create session
$sessionLifetime = $remember ? SESSION_LIFETIME : (60 * 60 * 24); // 7 days or 1 day
$expiresAt       = time() + $sessionLifetime;
$sessionToken    = generateToken();

FaithGuardRepository::createSession($sessionToken, $user['id'], $expiresAt);
setSessionCookie($sessionToken, $expiresAt);

// Page guards use PHP session state, while API authentication uses fg_session.
// Keep both in sync after a successful login.
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['logged_in'] = true;

// Regenerate PHP session id after successful authentication
if (session_status() === PHP_SESSION_ACTIVE) {
    session_regenerate_id(true);
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Return user data (exclude sensitive fields)
successResponse([
    'user' => [
        'id'         => $user['id'],
        'email'      => $user['email'],
        'first_name' => $user['first_name'],
        'last_name'  => $user['last_name'],
        'avatar_url' => $user['avatar_url'],
        'is_admin'   => (bool) $user['is_admin'],
    ],
]);
