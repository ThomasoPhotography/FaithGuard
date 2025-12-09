<?php
session_start();
require_once __DIR__ . '/../../db/database.php';
header('Content-Type: application/json');

$debug = false; // Set to true for debugging, then false for production

$data = json_decode(file_get_contents('php://input'), true);
if ($debug) {
    error_log('Decoded data: ' . print_r($data, true));
}

if (! $data || json_last_error() !== JSON_ERROR_NONE || ! isset($data['email'], $data['password'])) {
    $response = ['error' => 'Invalid input'];
    if ($debug) {
        $response['debug'] = [
            'json_error' => json_last_error_msg(),
            'data'       => $data,
        ];
    }
    echo json_encode($response);
    exit;
}

// Sanitize inputs
$email    = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$password = $data['password'];

$user = Database::getSingleRow("SELECT * FROM users WHERE email = ?", [$email]);

// If email doesn't exist, redirect to register.php
if (!$user) {
    header('Location: /register.php');
    exit;
}

// If email exists, check password
if (password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['token']     = bin2hex(random_bytes(16));
    $_SESSION['logged_in'] = true; // ADD THIS
    echo json_encode(['success' => true, 'token' => $_SESSION['token']]);
} else {
    echo json_encode(['error' => 'Invalid credentials']);
}
