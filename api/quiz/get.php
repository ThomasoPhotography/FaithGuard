<?php
/**
 * GET ENDPOINT: Fetches all quiz questions and options.
 */

// --- Core App Requirements ---
require_once __DIR__ . "/../../db/database.php";
require_once __DIR__ . "/../../db/FaithGuardRepository.php";
require_once __DIR__ . "/../helper/debug.php";

session_set_cookie_params([
    'lifetime' => 302400, // 3.5 days (84 hours)
    'path'     => '/',
    'domain'   => $_SERVER['SERVER_NAME'] ?? '',
    'secure'   => true,
    'httponly' => true,
]);
session_start();

header('Content-Type: application/json');

// 1. Authorization Check
if (! isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized. Please log in to save results.']);
    exit;
}

// 2. Request Method Check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

try {
    // 3. Get Input Data
    $json_input = file_get_contents('php://input');
    $data       = json_decode($json_input, true);

    // Validate essential fields
    if (! isset($data['addiction_type']) || ! isset($data['answers']) || ! is_array($data['answers'])) {
        throw new Exception("Invalid input format. 'addiction_type' and 'answers' array are required.");
    }

    // Replace deprecated FILTER_SANITIZE_STRING with htmlspecialchars()
    $addictionType = htmlspecialchars($data['addiction_type'], ENT_QUOTES, 'UTF-8');
    $answers       = $data['answers']; // Array of objects like {question_id: 1, score: 3} or just scores

    // 4. Calculate Total Score
    // Assuming 'answers' is an array of numerical scores based on the 1-5 scale
    // If it's an object, we extract the values.
    $totalScore = 0;
    foreach ($answers as $ans) {
        // Handle if answer is just a number or an object with a 'score' key
        $val = is_array($ans) ? ($ans['score'] ?? 0) : $ans;
        $totalScore += (float) $val;
    }

    // 5. Store in Database via Repository
    // Signature: createQuizResult($userId, $addictionType, $answersJson, $totalScore)
    $result = FaithGuardRepository::createQuizResult(
        $_SESSION['user_id'],
        $addictionType,
        json_encode($answers), // Store raw answers for detail view later
        $totalScore
    );

    if ($result) {
        $recommendedTags = [$addictionType];

        echo json_encode([
            'success'          => true,
            'message'          => 'Quiz result saved successfully.',
            'score'            => $totalScore,
            'recommended_tags' => $recommendedTags,
        ]);
    } else {
        throw new Exception("Database insertion failed.");
    }

} catch (Exception $e) {
    error_log("Quiz Submit Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred while saving your results.']);
}
