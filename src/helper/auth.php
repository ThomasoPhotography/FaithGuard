<?php
session_start();
require_once '../repository/repository.php';  // Adjusted to relative path (one dir up; change if needed)

header('Content-Type: application/json');

// Handle logout first (works for GET or POST)
if (isset($_GET['logout']) || isset($_POST['logout'])) {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out.']);
    exit;
}

// For login/sign-up, require POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method for login/sign-up.']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
    exit;
}

// Check if user exists for login
$user = FaithGuardRepository::getUserByUsername($username);
if ($user) {
    // User exists: attempt login
    if (password_verify($password, $user->password)) {  // Verify against the hashed password in 'password' column
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['username'] = $user->username;
        $_SESSION['is_admin'] = $user->is_admin;  // Store admin status
        echo json_encode(['success' => true, 'message' => 'Login successful.', 'username' => $user->username]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid password.']);
    }
} else {
    // User doesn't exist: sign up
    $password_hash = password_hash($password, PASSWORD_DEFAULT);  // Hash the password securely
    $rowsAffected = FaithGuardRepository::addUser($username, $password_hash, false);  // Default to non-admin, stores in 'password' column
    echo $password_hash;
    if ($rowsAffected > 0) {
        // Retrieve the new user to get the ID
        $newUser = FaithGuardRepository::getUserByUsername($username);
        $_SESSION['user_id'] = $newUser->user_id;
        $_SESSION['username'] = $newUser->username;
        $_SESSION['is_admin'] = $newUser->is_admin;
        echo json_encode(['success' => true, 'message' => 'Sign up successful.', 'username' => $newUser->username]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sign up failed.']);
    }
}
?>