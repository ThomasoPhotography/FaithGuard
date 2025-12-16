<?php
session_set_cookie_params(['path' => '/', 'secure' => true, 'httponly' => true]);
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
$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$name = trim($input['name'] ?? null);

// Validation
$errors = [];
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required.';
}
if (empty($password) || strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}
if (!empty($name) && strlen($name) > 255) {
    $errors[] = 'Name must be 255 characters or less.';
}

// Check for existing user
if (empty($errors)) {
    $existingUser = FaithGuardRepository::getUserByEmail($email);
    if ($existingUser) {
        $errors[] = 'An account with this email already exists.';
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
    exit();
}

// Hash password and create user
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$userCreated = FaithGuardRepository::createUser($email, $passwordHash, $name);

if ($userCreated) {
    // Optional: Auto-login after registration (set session like login.php)
    $newUser = FaithGuardRepository::getUserByEmail($email);
    if ($newUser) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $newUser['id'];
    }
    
    echo json_encode(['success' => true, 'message' => 'Registration successful. Welcome to FaithGuard!']);
} else {
    error_log("Registration failed for email: $email"); // Log for debugging
    echo json_encode(['success' => false, 'error' => 'Registration failed. Please try again.']);
}
?>