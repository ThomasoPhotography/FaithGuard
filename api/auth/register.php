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
require_once __DIR__ . '/../rate_limiter.php';

startAppSession();

file_put_contents(
    __DIR__ . '/register_debug.txt',
    "SESSION STARTED\n",
    FILE_APPEND
);

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

    file_put_contents(
        __DIR__ . '/register_debug.txt',
        "GET REQUEST\n",
        FILE_APPEND
    );

    header('Content-Type: text/html; charset=utf-8');

    try {

        $csrf = FaithGuardRepository::createCsrfToken(
            $_SESSION['user_id'] ?? null,
            3600
        );

        if (! $csrf) {
            throw new Exception(
                'CSRF token creation failed'
            );
        }

    } catch (Throwable $e) {

        file_put_contents(
            __DIR__ . '/register_debug.txt',
            "CSRF ERROR: " . $e->getMessage() . "\n",
            FILE_APPEND
        );

        http_response_code(500);
        echo "Unable to load registration form.";
        exit;
    }

    $csrf = htmlspecialchars(
        $csrf,
        ENT_QUOTES,
        'UTF-8'
    );

    echo <<<HTML

<div class="modal fade" id="registerModal" tabindex="-1">

<div class="modal-dialog">

<div class="modal-content">


<form id="registerForm">


<div class="modal-header">

<h5 class="modal-title">
Create account
</h5>

<button
type="button"
class="btn-close"
data-bs-dismiss="modal">
</button>

</div>



<div class="modal-body">


<div class="c-form__message mb-3"></div>


<div class="mb-3">
<label for="first_name">
First name
</label>

<input
class="form-control"
name="first_name"
required>
</div>



<div class="mb-3">
<label for="last_name">
Last name
</label>

<input
class="form-control"
name="last_name"
required>
</div>



<div class="mb-3">
<label for="email">
Email
</label>

<input
type="email"
class="form-control"
name="email"
required>
</div>



<div class="mb-3">
<label for="password">
Password
</label>

<input
type="password"
class="form-control"
name="password"
required>
</div>


<input
type="hidden"
name="csrf_token"
value="$csrf">


</div>



<div class="modal-footer">

<button
type="submit"
class="btn btn-primary">

Create account

</button>


<button
type="button"
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>

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

    errorResponse(
        'Method not allowed',
        405
    );

}

try {

    file_put_contents(
        __DIR__ . '/register_debug.txt',
        "POST START\n",
        FILE_APPEND
    );

    if (! ENABLE_REGISTRATION) {

        errorResponse(
            'Registration disabled',
            403
        );

    }

/*
|--------------------------------------------------------------------------
| Read JSON
|--------------------------------------------------------------------------
*/

    $body = file_get_contents(
        "php://input"
    );

    file_put_contents(
        __DIR__ . '/register_debug.txt',
        "BODY:\n" . $body . "\n",
        FILE_APPEND
    );

    $data = json_decode(
        $body,
        true
    );

    if (! is_array($data)) {

        errorResponse(
            'Invalid JSON',
            400
        );

    }

    validateRequired(
        $data,
        [
            'first_name',
            'last_name',
            'email',
            'password',
            'csrf_token',
        ]
    );

/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
*/

    if (
        ! FaithGuardRepository::validateCsrfToken(
            $data['csrf_token']
        )
    ) {

        errorResponse(
            'Invalid CSRF token',
            403
        );

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

    if (
        mb_strlen($firstName) < 2 ||
        mb_strlen($firstName) > 50
    ) {

        errorResponse(
            'Invalid first name'
        );

    }

    if (
        mb_strlen($lastName) < 2 ||
        mb_strlen($lastName) > 50
    ) {

        errorResponse(
            'Invalid last name'
        );

    }

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {

        errorResponse(
            'Invalid email'
        );

    }

    if (
        strlen($password) < PASSWORD_MIN_LENGTH
    ) {

        errorResponse(
            'Password too short'
        );

    }

/*
|--------------------------------------------------------------------------
| Existing account
|--------------------------------------------------------------------------
*/

    if (
        FaithGuardRepository::getUserByEmail($email)
    ) {

        errorResponse(
            'Email already registered'
        );

    }

/*
|--------------------------------------------------------------------------
| Create account
|--------------------------------------------------------------------------
*/

    $passwordHash = password_hash(
        $password,
        PASSWORD_BCRYPT
    );

    file_put_contents(
        __DIR__ . '/register_debug.txt',
        "PASSWORD HASH CREATED\n",
        FILE_APPEND
    );

    $userId = FaithGuardRepository::createUser([

        'first_name'    =>
        $firstName,

        'last_name'     =>
        $lastName,

        'email'         =>
        $email,

        'password_hash' =>
        $passwordHash,

    ]);

    if (! $userId) {

        errorResponse(
            'Could not create user',
            500
        );

    }

/*
|--------------------------------------------------------------------------
| Login session
|--------------------------------------------------------------------------
*/

    FaithGuardRepository::consumeCsrfToken(
        $data['csrf_token']
    );

    $sessionToken = bin2hex(
        random_bytes(32)
    );

    $expires = gmdate(
        'Y-m-d H:i:s',
        time() + SESSION_LIFETIME
    );

    FaithGuardRepository::createSession(
        $sessionToken,
        $userId,
        $expires
    );

    setSessionCookie(
        $sessionToken,
        strtotime($expires)
    );

    $_SESSION['user_id'] = $userId;

    successResponse([

        'user' => [

            'id'         => $userId,

            'email'      => $email,

            'first_name' => $firstName,

            'last_name'  => $lastName,

        ],

    ]);

} catch (Throwable $e) {

    file_put_contents(
        __DIR__ . '/register_debug.txt',
        "ERROR: " . $e->getMessage() . "\n",
        FILE_APPEND
    );

    errorResponse(
        'Server error',
        500
    );

}
