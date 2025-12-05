<?php
session_start();
header('Content-Type: application/json');

// --- IMPORTANT: FIX PATHS ---
// Assumes this file is in /api/auth/, so we go up two levels (../../) to reach /www/, then into /db/
require_once __DIR__ . '/../../db/database.php';
require_once __DIR__ . '/../../db/FaithGuardRepository.php'; // Include repository for data access

// --- 1. Input Handling and Validation ---
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!isset($data['email'], $data['password']) || empty($data['email']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'error' => 'Missing email or password.']);
    exit;
}

$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$password = $data['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email format.']);
    exit;
}

// --- 2. Check for Existing User ---
// Assuming FaithGuardRepository or Database has a method to fetch a user by email
$existing_user = Database::getSingleRow("SELECT id FROM users WHERE email = ?", [$email]);

if ($existing_user) {
    // If the user already exists, prevent re-registration
    echo json_encode(['success' => false, 'error' => 'User already exists.']);
    exit;
}

// --- 3. Hash Password and Insert ---
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$result = Database::execute("INSERT INTO users (email, password_hash) VALUES (?, ?)", [$email, $hashed_password]);

if ($result > 0) {
    // --- 4. SUCCESS: Get User ID and Establish Session ---
    
    // Fetch the newly created user's ID
    // (This requires another quick DB call or using lastInsertId, but this is safer)
    $new_user = Database::getSingleRow("SELECT id FROM users WHERE email = ?", [$email]);

    if ($new_user) {
        // Clear potential old session data and set new login state
        $_SESSION = [];
        $_SESSION['user_id'] = $new_user['id'];
        $_SESSION['logged_in'] = true;
        session_regenerate_id(true);
        
        echo json_encode(['success' => true]);
        
    } else {
        // Registration successful but unable to fetch user for login (DB issue)
        echo json_encode(['success' => false, 'error' => 'Registration succeeded, but failed to log in.']);
    }
} else {
    // Failure to insert user into the database
    echo json_encode(['success' => false, 'error' => 'Database insert failed.']);
}
?>