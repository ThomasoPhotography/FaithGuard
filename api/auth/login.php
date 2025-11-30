<?php
/**
 * LOGIN ENDPOINT: Authenticates user and establishes a PHP session.
 */
session_start();
// The Database class is likely defined in database.php, 
// but often the data access methods are in the repository.
require_once __DIR__ . '/../../db/database.php';
require_once __DIR__ . '/../../db/FaithGuardRepository.php'; // <-- ADDED: Ensure repository is loaded

header('Content-Type: application/json');

$debug = false; 

$data = json_decode(file_get_contents('php://input'), true);

// --- 1. Input Validation ---
if (!$data || json_last_error() !== JSON_ERROR_NONE || !isset($data['email'], $data['password'])) {
    $response = ['success' => false, 'error' => 'Invalid input'];
    if ($debug) {
        $response['debug'] = ['json_error' => json_last_error_msg(), 'data' => $data];
    }
    echo json_encode($response);
    exit;
}

// Sanitize inputs
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$password = $data['password'];

// --- 2. Authentication ---
// Ensure getSingleRow is a valid static method in Database class
$user = Database::getSingleRow("SELECT id, email, password_hash FROM users WHERE email = ?", [$email]);

if ($user && password_verify($password, $user['password_hash'])) {
    
    // --- 3. SUCCESS: Set Session Flags ---
    
    // 1. Clear any old session data first (best practice)
    $_SESSION = []; 
    
    // 2. Set the essential persistence flag (used by index.php)
    $_SESSION['logged_in'] = true; 
    $_SESSION['user_id'] = $user['id'];
    
    // 3. (Optional) Regenerate session ID for security
    session_regenerate_id(true);

    // 4. Set the token (optional, but keep it for JS validation)
    $_SESSION['token'] = bin2hex(random_bytes(16));
    
    echo json_encode(['success' => true, 'token' => $_SESSION['token']]);
    
} else {
    // Failure: Invalid credentials
    echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
}
?>