<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function registerDebug($message)
{
    file_put_contents(
        __DIR__ . '/register_debug.txt',
        date('Y-m-d H:i:s') . " " . $message . "\n",
        FILE_APPEND
    );
}


registerDebug("REGISTER HIT");


require_once __DIR__ . '/../base.php';
registerDebug("BASE LOADED");


require_once __DIR__ . '/../rate_limiter.php';
registerDebug("RATE LIMITER LOADED");


startAppSession();
registerDebug("SESSION STARTED");



if (!defined('PASSWORD_MIN_LENGTH')) {
    define('PASSWORD_MIN_LENGTH', 8);
}

if (!defined('PASSWORD_MAX_LENGTH')) {
    define('PASSWORD_MAX_LENGTH', 72);
}


/*
|--------------------------------------------------------------------------
| Use BCRYPT for Combell/shared hosting compatibility
|--------------------------------------------------------------------------
*/

if (!defined('PASSWORD_ALGORITHM')) {
    define(
        'PASSWORD_ALGORITHM',
        PASSWORD_BCRYPT
    );
}


registerDebug(
    "REQUEST METHOD: " . $_SERVER['REQUEST_METHOD']
);



/*
|--------------------------------------------------------------------------
| GET - Load registration modal
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    registerDebug("ENTER GET");


    header(
        'Content-Type: text/html; charset=utf-8'
    );


    try {

        registerDebug("BEFORE CSRF");


        $csrf = FaithGuardRepository::createCsrfToken(
            $_SESSION['user_id'] ?? null,
            3600
        );


        registerDebug(
            "AFTER CSRF " . ($csrf ? "SUCCESS" : "FAILED")
        );


        if (!$csrf) {
            throw new Exception(
                "CSRF token empty"
            );
        }


    } catch(Throwable $e) {


        registerDebug(
            "CSRF ERROR: " . $e->getMessage()
        );


        http_response_code(500);

        echo "Could not create registration form";

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



<input 
class="form-control mb-3"
name="first_name"
placeholder="First name"
required>


<input 
class="form-control mb-3"
name="last_name"
placeholder="Last name"
required>


<input 
class="form-control mb-3"
type="email"
name="email"
placeholder="Email"
required>


<input 
class="form-control mb-3"
type="password"
name="password"
placeholder="Password"
required>



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
        "Method not allowed",
        405
    );

}



try {


registerDebug("POST START");



if (!ENABLE_REGISTRATION) {

    errorResponse(
        "Registration disabled",
        403
    );

}



$input = getJsonInput();


registerDebug(
    "INPUT: " . print_r($input,true)
);



$token = getCsrfTokenFromRequest(
    $input
);



if (
    empty($token) ||
    !FaithGuardRepository::validateCsrfToken($token)
) {

    registerDebug("CSRF FAILED");

    errorResponse(
        "Invalid CSRF token",
        403
    );

}



$firstName =
trim($input['first_name'] ?? '');

$lastName =
trim($input['last_name'] ?? '');

$email =
trim($input['email'] ?? '');

$password =
$input['password'] ?? '';



if (
    mb_strlen($firstName)<2 ||
    mb_strlen($firstName)>50
) {

    errorResponse(
        "Invalid first name"
    );

}


if (
    mb_strlen($lastName)<2 ||
    mb_strlen($lastName)>50
) {

    errorResponse(
        "Invalid last name"
    );

}


if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {

    errorResponse(
        "Invalid email"
    );

}


if (
    strlen($password)<PASSWORD_MIN_LENGTH
) {

    errorResponse(
        "Password too short"
    );

}




if (
    FaithGuardRepository::getUserByEmail($email)
) {

    errorResponse(
        "Email already registered"
    );

}



$passwordHash =
password_hash(
    $password,
    PASSWORD_ALGORITHM
);



registerDebug("PASSWORD HASH CREATED");



$userId =
FaithGuardRepository::createUser([

    'first_name'=>$firstName,

    'last_name'=>$lastName,

    'email'=>$email,

    'password_hash'=>$passwordHash

]);



registerDebug(
    "USER ID: ".$userId
);



if(!$userId){

    errorResponse(
        "Unable to create account",
        500
    );

}



FaithGuardRepository::consumeCsrfToken(
    $token
);



$sessionToken =
bin2hex(
    random_bytes(32)
);



$expires =
gmdate(
    'Y-m-d H:i:s',
    time()+SESSION_LIFETIME
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



$_SESSION['user_id']=$userId;



successResponse([

'user'=>[

'id'=>$userId,

'email'=>$email,

'first_name'=>$firstName,

'last_name'=>$lastName

]

]);


}
catch(Throwable $e){


registerDebug(
"ERROR: ".$e->getMessage()
);


registerDebug(
$e->getTraceAsString()
);


errorResponse(
"Server error",
500
);


}