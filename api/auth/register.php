<?php

file_put_contents(
    __DIR__ . '/register_debug.txt',
    date('Y-m-d H:i:s') . " REGISTER HIT\n",
    FILE_APPEND
);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../base.php';

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "BASE LOADED\n",
    FILE_APPEND
);

require_once __DIR__ . '/../rate_limiter.php';

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "RATE LIMITER LOADED\n",
    FILE_APPEND
);

startAppSession();

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "SESSION STARTED\n",
    FILE_APPEND
);

file_put_contents(__DIR__ . '/register_debug.txt', "STEP A\n", FILE_APPEND);

if (! defined('PASSWORD_MIN_LENGTH')) {
    define('PASSWORD_MIN_LENGTH', 8);
}

file_put_contents(__DIR__ . '/register_debug.txt', "STEP B\n", FILE_APPEND);

if (! defined('PASSWORD_MAX_LENGTH')) {
    define('PASSWORD_MAX_LENGTH', 72);
}

file_put_contents(__DIR__ . '/register_debug.txt', "STEP C\n", FILE_APPEND);

if (! defined('PASSWORD_ALGORITHM')) {

    file_put_contents(__DIR__ . '/register_debug.txt', "STEP D\n", FILE_APPEND);

    if (defined('PASSWORD_ARGON2ID')) {

        file_put_contents(__DIR__ . '/register_debug.txt', "STEP E\n", FILE_APPEND);

        define('PASSWORD_ALGORITHM', PASSWORD_ARGON2ID);

    } else {

        file_put_contents(__DIR__ . '/register_debug.txt', "STEP F\n", FILE_APPEND);

        define('PASSWORD_ALGORITHM', PASSWORD_DEFAULT);
    }
}

file_put_contents(__DIR__ . '/register_debug.txt', "STEP G\n", FILE_APPEND);

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "REQUEST: " . $_SERVER['REQUEST_METHOD'] . "\n",
    FILE_APPEND
);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    file_put_contents(
        __DIR__ . '/register_debug.txt',
        "ENTERED GET\n",
        FILE_APPEND
    );
}

/*--------------------------------------------------------------------------
| Already logged in
--------------------------------------------------------------------------*/

if (! empty($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        header('Location: /dashboard.php');
        exit;
    }
}

/*--------------------------------------------------------------------------
| GET REQUEST
| Return registration modal HTML
--------------------------------------------------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: text/html; charset=utf-8');
    try {
        $csrf = FaithGuardRepository::createCsrfToken($_SESSION['user_id'] ?? null, 3600);
        if (! $csrf) {
            throw new Exception('Could not generate CSRF token');
        }
    } catch (Throwable $e) {
        error_log("CSRF generation failed: " . $e->getMessage());
        http_response_code(500);
        echo "Unable to load registration form.";
        exit;
    }

    $csrf = htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8');
    echo <<<HTML
        <div class="modal fade c-register" id="registerModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="registerForm">
                        <div class="modal-header c-register__header">
                            <h5 class="modal-title c-register__title">Create account</h5>
                            <button type="button" class="btn-close c-btn c-btn--secondary" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body c-register__body">
                            <div class="c-form__message c-register__message mb-3"></div>
                            <div class="mb-3">
                                <label class="form-label c-register__label">First name</label>
                                <input type="text" class="form-control c-register__input" name="first_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label c-register__label">Last name</label>
                                <input type="text" class="form-control c-register__input" name="last_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label c-register__label">Email</label>
                                <input type="email" class="form-control c-register__input" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label c-register__label">Password</label>
                                <input type="password" class="form-control c-register__input" name="password" required>
                            </div>
                            <input type="hidden" name="csrf_token" value="$csrf">
                        </div>
                        <div class="modal-footer c-register__footer">
                            <button type="submit" class="btn c-btn c-btn__submit">Create account</button>
                            <button type="button" class="btn btn-secondary c-btn c-btn__cancel" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        HTML;
    exit;
}

/*--------------------------------------------------------------------------
| POST REQUEST ONLY FROM HERE
--------------------------------------------------------------------------*/

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "AFTER GET BLOCK\n",
    FILE_APPEND
);

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "REQUEST METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n",
    FILE_APPEND
);

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "CONTENT TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'NONE') . "\n",
    FILE_APPEND
);

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "POST DATA: " . file_get_contents("php://input") . "\n",
    FILE_APPEND
);

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "REQUEST METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n",
    FILE_APPEND
);

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "ENTERING POST\n",
    FILE_APPEND
);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "BEFORE TRY BLOCK\n",
    FILE_APPEND
);

try {

    registerDebug("STEP 1 - TRY START");

    if (! ENABLE_REGISTRATION) {
        registerDebug("STEP 2 - REGISTRATION DISABLED");
        errorResponse('Registration is currently disabled', 403);
    }

    registerDebug("STEP 3 - REGISTRATION ENABLED");

    $input = getJsonInput();

    registerDebug("STEP 4 - JSON RECEIVED");
    registerDebug(print_r($input, true));

    $postedToken = getCsrfTokenFromRequest($input);

    registerDebug(
        "STEP 5 - TOKEN " .
        (empty($postedToken) ? "MISSING" : "FOUND")
    );

    validateRequired(
        $input,
        [
            'first_name',
            'last_name',
            'email',
            'password',
            'csrf_token',
        ]
    );

    registerDebug("STEP 6 - REQUIRED VALIDATION OK");

    if (
        empty($postedToken) ||
        ! FaithGuardRepository::validateCsrfToken($postedToken)
    ) {
        registerDebug("STEP 7 - CSRF FAILED");
        errorResponse('Invalid or expired CSRF token', 403);
    }

    registerDebug("STEP 8 - CSRF VALID");

    $firstName = trim($input['first_name']);
    $lastName  = trim($input['last_name']);
    $email     = trim($input['email']);
    $password  = $input['password'];

    registerDebug("STEP 9 - FIELDS EXTRACTED");

    if (FaithGuardRepository::getUserByEmail($email)) {
        registerDebug("STEP 10 - EMAIL EXISTS");
        errorResponse('Email is already registered');
    }

    registerDebug("STEP 11 - EMAIL AVAILABLE");

    $passwordHash = password_hash(
        $password,
        PASSWORD_ALGORITHM
    );

    registerDebug("STEP 12 - PASSWORD HASHED");

    $userId = FaithGuardRepository::createUser([
        'first_name'    => $firstName,
        'last_name'     => $lastName,
        'email'         => $email,
        'password_hash' => $passwordHash,
    ]);

    registerDebug("STEP 13 - CREATE USER RETURNED");
    registerDebug("USER ID = " . print_r($userId, true));

    if (! $userId) {
        registerDebug("STEP 14 - USER CREATION FAILED");
        errorResponse('Unable to create account', 500);
    }

    registerDebug("STEP 15 - USER CREATED");

    FaithGuardRepository::consumeCsrfToken($postedToken);

    registerDebug("STEP 16 - TOKEN CONSUMED");

    $token = bin2hex(random_bytes(32));

    registerDebug("STEP 17 - SESSION TOKEN CREATED");

    $expiresAt = gmdate(
        'Y-m-d H:i:s',
        time() + SESSION_LIFETIME
    );

    FaithGuardRepository::createSession(
        $token,
        $userId,
        $expiresAt
    );

    registerDebug("STEP 18 - SESSION CREATED");

    setSessionCookie(
        $token,
        strtotime($expiresAt)
    );

    registerDebug("STEP 19 - COOKIE SET");

    $_SESSION['user_id']   = $userId;
    $_SESSION['logged_in'] = true;

    registerDebug("STEP 20 - SESSION VARIABLES SET");

    successResponse([
        'user' => [
            'id'         => $userId,
            'email'      => $email,
            'first_name' => $firstName,
            'last_name'  => $lastName,
        ],
    ]);

} catch (Throwable $e) {

    registerDebug("EXCEPTION");

    registerDebug($e->getMessage());

    registerDebug($e->getFile());

    registerDebug("LINE " . $e->getLine());

    registerDebug($e->getTraceAsString());

    errorResponse(
        'Server error',
        500
    );
}
