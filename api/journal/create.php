<?php
// --- Core App Requirements ---
require_once __DIR__ . "/../../db/database.php";
require_once __DIR__ . "/../../db/FaithGuardRepository.php";
require_once __DIR__ . "/../helper/debug.php";

// Set session parameters and start session
session_set_cookie_params([
    'lifetime' => 302400, // 3.5 days
    'path'     => '/', 
    'domain'   => $_SERVER['HTTP_HOST'] ?? '',
    'secure'   => true, 
    'httponly' => true,
]);
session_start();

header('Content-Type: application/json');

/* =========================================================
   AUTHENTICATION CHECK
========================================================= */
if (!isset($_SESSION['user_id']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'You must be logged in to save a journal entry.']);
    exit;
}

/* =========================================================
   INPUT HANDLING
========================================================= */
$json_data = file_get_contents('php://input');
$payload = json_decode($json_data, true);
if (!$payload || !isset($payload['content'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid journal data provided.']);
    exit;
}
$userId = (int) $_SESSION['user_id'];
$content = trim($payload['content']);
$isRelated = (bool) ($payload['isRelated'] ?? false);
$addictionTypes = $payload['addictionTypes'] ?? []; // Array of selected addictions

/* =========================================================
   DATA PERSISTENCE
========================================================= */
try {
    $typesJson = json_encode($addictionTypes);
    $success = FaithGuardRepository::createJournalEntry(
        $userId, 
        $content, 
        $isRelated, 
        $typesJson
    );
    if (!$success) {
        throw new Exception("Database insertion failed.");
    }
    $responseData = ['success' => true];
    if ($isRelated) {
        $context = 'general';
        if (!empty($addictionTypes)) {
            $context = strtolower($addictionTypes[0]);
        }
        $encouragement = FaithGuardRepository::getEncouragement($context);
        if ($encouragement) {
            $responseData['scripture'] = [
                'text'  => $encouragement['text'],
                'verse' => $encouragement['verse']
            ];
        }
    }
    echo json_encode($responseData);
}
catch (Exception $e) {
    error_log("Journal API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An internal server error occurred while saving your entry.']);
}
?>