<?php
session_set_cookie_params([
    'lifetime' => 302400, // 3.5 days (84 hours)
    'path'     => '/',
    'domain'   => $_SERVER['SERVER_NAME'] ?? '',
    'secure'   => true,
    'httponly' => true,
]);
session_start();

require_once __DIR__ . '/../../db/FaithGuardRepository.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit();
}

// Get and sanitize input
$input    = json_decode(file_get_contents('php://input'), true);
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$name     = trim($input['name'] ?? null);

// Sanitize name (optional field) to prevent XSS on output
if (! empty($name)) {
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); // Escape for HTML output
}

// CAPTCHA Verification (if added from previous response)
$recaptchaResponse = $input['g-recaptcha-response'] ?? '';
if (! empty($recaptchaResponse)) {          // Only if CAPTCHA is enabled
    $recaptchaSecret = 'YOUR_SECRET_KEY_HERE'; // Replace with your key
    $recaptchaUrl    = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptchaData   = [
        'secret'   => $recaptchaSecret,
        'response' => $recaptchaResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ];
    $recaptchaOptions = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($recaptchaData),
        ],
    ];
    $recaptchaContext = stream_context_create($recaptchaOptions);
    $recaptchaResult  = file_get_contents($recaptchaUrl, false, $recaptchaContext);
    $recaptchaJson    = json_decode($recaptchaResult, true);
    if (! $recaptchaJson['success']) {
        echo json_encode(['success' => false, 'error' => 'CAPTCHA verification failed. Please try again.']);
        exit();
    }
}

// Validation
$errors = [];
if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required.';
}
if (empty($password) || strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}
if (! empty($name) && strlen($name) > 255) {
    $errors[] = 'Name must be 255 characters or less.';
}

// Check for existing user
if (empty($errors)) {
    $existingUser = FaithGuardRepository::getUserByEmail($email);
    if ($existingUser) {
        $errors[] = 'An account with this email already exists.';
    }
}

if (! empty($errors)) {
    echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
    exit();
}

// Hash password and create user
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$userCreated  = FaithGuardRepository::createUser($email, $passwordHash, $name);

if ($userCreated) {
    // Optional: Auto-login after registration
    $newUser = FaithGuardRepository::getUserByEmail($email);
    if ($newUser) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id']   = $newUser['id'];
    }

    echo json_encode(['success' => true, 'message' => 'Registration successful. Welcome to FaithGuard!']);
} else {
    error_log("Registration failed for email: $email"); // Log for debugging
    echo json_encode(['success' => false, 'error' => 'Registration failed. Please try again.']);
}
