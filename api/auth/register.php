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

if (! defined('PASSWORD_MIN_LENGTH')) {
    define('PASSWORD_MIN_LENGTH', 8);
}

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "PASSWORD CONSTANTS\n",
    FILE_APPEND
);

if (! defined('PASSWORD_MAX_LENGTH')) {
    define('PASSWORD_MAX_LENGTH', 72);
}

if (!defined('PASSWORD_ALGORITHM')) {
    if (defined('PASSWORD_ARGON2ID')) {
        define('PASSWORD_ALGORITHM', PASSWORD_ARGON2ID);
    } else {
        define('PASSWORD_ALGORITHM', PASSWORD_DEFAULT);
    }
}
file_put_contents(
    __DIR__ . '/register_debug.txt',
    "ARGON2 CHECK PASSED\n",
    FILE_APPEND
);

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
    "REQUEST METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n",
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

    file_put_contents(
        __DIR__ . '/register_debug.txt',
        date('Y-m-d H:i:s') . " STEP 1 - TRY START\n",
        FILE_APPEND
    );


    if (! ENABLE_REGISTRATION) {

        file_put_contents(
            __DIR__ . '/register_debug.txt',
            "STEP 2 - REGISTRATION DISABLED\n",
            FILE_APPEND
        );

        errorResponse('Registration is currently disabled', 403);
    }


    file_put_contents(
        __DIR__ . '/register_debug.txt',
        "STEP 3 - ENABLE_REGISTRATION OK\n",
        FILE_APPEND
    );


    $input = getJsonInput();


    file_put_contents(
        __DIR__ . '/register_debug.txt',
        "STEP 4 - JSON INPUT OK\n" .
        print_r($input, true) .
        "\n",
        FILE_APPEND
    );


    $postedToken = getCsrfTokenFromRequest($input);


    file_put_contents(
        __DIR__ . '/register_debug.txt',
        "STEP 5 - TOKEN RECEIVED: " .
        ($postedToken ? "YES" : "NO") .
        "\n",
        FILE_APPEND
    );


    validateRequired(
        $input,
        [
            'first_name',
            'last_name',
            'email',
            'password',
            'csrf_token'
        ]
    );


    file_put_contents(
        __DIR__ . '/register_debug.txt',
        "STEP 6 - REQUIRED VALIDATION OK\n",
        FILE_APPEND
    );

    /*if (mb_strlen($firstName) < 2 || mb_strlen($firstName) > 50) {
        errorResponse('First name must be between 2 and 50 characters');
    }

    if (mb_strlen($lastName) < 2 || mb_strlen($lastName) > 50) {
        errorResponse('Last name must be between 2 and 50 characters');
    }

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        errorResponse('Invalid email address');
    }

    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        errorResponse('Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters');
    }

    if (strlen($password) > PASSWORD_MAX_LENGTH) {
        errorResponse('Password must be no more than ' . PASSWORD_MAX_LENGTH . ' characters');
    }*/

    /*--------------------------------------------------------------------------
    | Existing user
    --------------------------------------------------------------------------*/

    /*if (FaithGuardRepository::getUserByEmail($email)) {
        errorResponse('Email is already registered');
    }*/

    /*--------------------------------------------------------------------------
    | Create user
    --------------------------------------------------------------------------*/

    /*$passwordHash = password_hash($password, PASSWORD_ALGORITHM);
    echo "POST STEP 5 - BEFORE CREATE USER<br>";
    $userId = FaithGuardRepository::createUser([
        'first_name'    => $firstName,
        'last_name'     => $lastName,
        'email'         => $email,
        'password_hash' => $passwordHash,

    ]);
    echo "POST STEP 6 - AFTER CREATE USER<br>";

    if (! $userId) {
        errorResponse(
            'Unable to create account',
            500
        );
    }

    FaithGuardRepository::consumeCsrfToken($postedToken);*/

    /*--------------------------------------------------------------------------
    | Session creation
    --------------------------------------------------------------------------*/
    /*$token = bin2hex(
        random_bytes(32)
    );

    $expiresAt = gmdate(
        'Y-m-d H:i:s',
        time() + SESSION_LIFETIME
    );
    echo "POST STEP 7 - BEFORE CREATE SESSION<br>";
    FaithGuardRepository::createSession(
        $token,
        $userId,
        $expiresAt
    );
    echo "POST STEP 8 - AFTER CREATE SESSION<br>";
    echo "POST STOP";

    setSessionCookie(
        $token,
        strtotime($expiresAt)
    );

    $_SESSION['user_id']   = $userId;
    $_SESSION['logged_in'] = true;

    successResponse([
        'user' => [
            'id'         => $userId,
            'email'      => $email,
            'first_name' => $firstName,
            'last_name'  => $lastName,
        ],
    ]);
} catch (Throwable $e) {
    echo "<pre>";
    echo "REGISTER ERROR\n\n";
    echo "Message: " . $e->getMessage() . "\n\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    echo "Trace:\n";
    echo $e->getTraceAsString();
    echo "</pre>";

    error_log(
        "REGISTER ERROR: " . $e->getMessage()
    );
    /*error_log(
        "REGISTER ERROR: " .
        $e->getMessage()
    );

    errorResponse(
        'Server error',
        500
    );*/

}