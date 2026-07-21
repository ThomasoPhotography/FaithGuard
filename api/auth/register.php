<?php
require_once __DIR__ . '/../base.php';
require_once __DIR__ . '/../rate_limiter.php';

startAppSession();
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

    // Generate a new CSRF token and store it in the database
    // CSRF tokens expire after 1 hour
    $csrf = FaithGuardRepository::createCsrfToken(null, 3600);

    if (empty($csrf)) {
        errorResponse('Failed to generate CSRF token', 500);
    }

    $csrfHtml = htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8');

    // Emit a Bootstrap modal fragment and include the register.js bootstrapper
    echo <<<HTML
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content c-register__modal">
            <form id="registerForm" class="c-form__register">
                <div class="modal-header c-register__modal--header">
                    <h5 class="modal-title c-register__modal--title">Create account</h5>
                    <button type="button" class="btn-close c-btn c-btn__close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <input type="hidden" name="csrf_token" value="$csrfHtml">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn c-btn c-btn__submit">Create account</button>
                    <button type="button" class="btn c-btn c-btn__cancel" data-bs-dismiss="modal">Cancel</button>
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

try {
    if (! ENABLE_REGISTRATION) {
        errorResponse('Registration is currently disabled', 403);
    }

    startAppSession();
    $input       = getJsonInput();
    $postedToken = getCsrfTokenFromRequest($input);

    // Accept first_name/last_name (single input) or full_name (preferred)
    if (! empty($input['full_name'])) {
        $fullName = trim($input['full_name']);
        // Attempt to split full name into first and last name (simple heuristic)
        $parts     = preg_split('/\s+/', $fullName, 2);
        $firstName = $parts[0] ?? '';
        $lastName  = $parts[1] ?? '';
    } else {
        $firstName = trim($input['first_name'] ?? '');
        $lastName  = trim($input['last_name'] ?? '');
        $fullName  = trim($firstName . ' ' . $lastName);
    }
    validateRequired($input, ['first_name', 'last_name', 'email', 'password', 'csrf_token']);

    // CSRF validation - validate against database
    if (empty($postedToken) || ! FaithGuardRepository::validateCsrfToken($postedToken)) {
        errorResponse('Invalid or expired CSRF token', 403);
    }

    // Input validation
    $firstName   = trim($input['first_name'] ?? '');
    $lastName    = trim($input['last_name'] ?? '');
    $email       = trim($input['email'] ?? '');
    $rawPassword = $input['password'] ?? '';
    // Add first and last name to full name
    $fullName = trim($firstName . ' ' . $lastName);

    // Validate first name
    if (mb_strlen($firstName) < 2 || mb_strlen($firstName) > 50) {
        errorResponse('First name must be between 2 and 50 characters');
    }

    // Validate last name
    if (mb_strlen($lastName) < 2 || mb_strlen($lastName) > 50) {
        errorResponse('Last name must be between 2 and 50 characters');
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
        errorResponse('We could not create your account. Please verify your database connection and try again.', 500);
    }

    // Consume the CSRF token (prevent replay attacks)
    FaithGuardRepository::consumeCsrfToken($postedToken);

    // Create session
    $expiresAt    = time() + SESSION_LIFETIME;
    $sessionToken = generateToken();

    FaithGuardRepository::createSession($sessionToken, $userId, $expiresAt);
    setSessionCookie($sessionToken, $expiresAt);
    $_SESSION['user_id']   = (int) $userId;
    $_SESSION['logged_in'] = true;

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
} catch (\Throwable $e) {
    error_log('Register error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    if ((defined('APP_DEBUG') && APP_DEBUG) || (isset($_GET['debug']) && $_GET['debug'] == '1')) {
        errorResponse('Server error: ' . $e->getMessage(), 500);
    }

    if (stripos($e->getMessage(), 'SQLSTATE') !== false || stripos($e->getMessage(), 'PDO') !== false || stripos($e->getMessage(), 'Database connection failed') !== false) {
        errorResponse('We could not connect to the database. Please verify your database credentials and try again.', 500);
    }

    errorResponse('Server error', 500);
}
