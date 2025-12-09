<?php
session_set_cookie_params([
    'lifetime' => 302400, // 3.5 days (84 hours)
    'path' => '/',       
    'domain' => $_SERVER['HTTP_HOST'] ?? '',
    'secure' => true,    
    'httponly' => true
]);
session_start();
require_once __DIR__ . '/../../db/database.php';
require_once __DIR__ . '/../../db/FaithGuardRepository.php'; 
require_once __DIR__ . '/../helper/debug.php'; 

header('Content-Type: application/json');

$debug = true;

// --- 1. Handle Input (Reads JSON sent by JavaScript) ---
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data || json_last_error() !== JSON_ERROR_NONE || !isset($data['email'], $data['password'])) {
    $response = ['success' => false, 'error' => 'Invalid input format or missing fields.'];
    echo json_encode($response);
    exit;
}

// Sanitize inputs
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$password = $data['password'];

// --- 2. Authentication Logic ---
try {
    $user = FaithGuardRepository::getUserByEmail($email);
} catch (Throwable $e) {
    // Database error handling (returns 500 equivalent)
    error_log("FATAL LOGIN ERROR (DB/REPO): " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server Configuration Error.']);
    exit;
}

// Change logic flow in three parts:
// A. User not found -> Return specific JSON error (SKIP REDIRECT)
if (!$user) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}

// B. User found -> Check password
if (password_verify($password, $user['password_hash'])) {
    
    // --- SUCCESS: Establish Session ---
    $_SESSION = []; 
    $_SESSION['logged_in'] = true; 
    $_SESSION['user_id'] = $user['id'];
    
    session_regenerate_id(true);

    $_SESSION['token'] = bin2hex(random_bytes(16));
    
    echo json_encode(['success' => true, 'token' => $_SESSION['token']]);
    
} else {
    // C. User found, but invalid password
    echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
}