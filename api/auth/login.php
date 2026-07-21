<?php
require_once __DIR__ . '/../base.php';
require_once __DIR__ . '/../rate_limiter.php'; // Limiter

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

startAppSession();
validateCsrfToken();

$input = getJsonInput();
validateRequired($input, ['email', 'password']);

$email = filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL);
if (! $email) {
    RateLimiter::registerFailure($_SERVER['REMOTE_ADDR'], 300);
    errorResponse('Invalid email address');
}

$password = $input['password'];
$remember = $input['remember'] ?? false;

// Rate limiting check - prevent BFA's
$maxAttempts = 5;
if (! RateLimiter::isAllowed($_SERVER['REMOTE_ADDR'], $maxAttempts, 300)) {
    errorResponse('Too many login attempts. Please try again in ' . floor(300 - time() % 300) . ' seconds.', 429);
}

// Find user
$user = FaithGuardRepository::getUserByEmail($email);
if (! $user) {
    RateLimiter::registerFailure($_SERVER['REMOTE_ADDR'], 300);
    errorResponse('Invalid email or password', 401);
}

// Verify password
if (! password_verify($password, $user['password_hash'])) {
    RateLimiter::registerFailure($_SERVER['REMOTE_ADDR'], 300);
    errorResponse('Invalid email or password', 401);
}

// Update last login
FaithGuardRepository::updateLastLogin($user['id']);
RateLimiter::reset($_SERVER['REMOTE_ADDR']);

// Create session
$sessionLifetime = $remember ? SESSION_LIFETIME : (60 * 60 * 24); // 7 days or 1 day
$expiresAt       = time() + $sessionLifetime;
$sessionToken    = generateToken();

FaithGuardRepository::createSession($sessionToken, $user['id'], $expiresAt);
setSessionCookie($sessionToken, $expiresAt);

// Page guards use PHP session state, while API authentication uses fg_session.
// Keep both in sync after a successful login.
$_SESSION['user_id']   = (int) $user['id'];
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
