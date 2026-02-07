<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

if (!ENABLE_QUIZ) {
    errorResponse('Quiz is currently disabled', 403);
}

$user = requireAuth();
$input = getJsonInput();

if (empty($input['answers']) || !is_array($input['answers'])) {
    errorResponse('Answers array required');
}

$answers = $input['answers'];
$totalScore = 0;
$categoryScores = [];

// Calculate score
foreach ($answers as $answer) {
    $questionId = (int)($answer['question_id'] ?? 0);
    $answerId = (int)($answer['answer_id'] ?? 0);
    
    if (!$questionId || !$answerId) {
        continue;
    }
    
    // Get answer score from database
    $answerData = Database::getSingleRow(
        "SELECT qa.score_value, qq.category FROM quiz_answers qa JOIN quiz_questions qq ON qa.question_id = qq.id WHERE qa.id = ? AND qa.question_id = ?",
        [$answerId, $questionId]
    );
    
    if ($answerData) {
        $score = (int)$answerData['score_value'];
        $category = $answerData['category'];
        
        $totalScore += $score;
        
        if (!isset($categoryScores[$category])) {
            $categoryScores[$category] = 0;
        }
        $categoryScores[$category] += $score;
    }
}

// Generate recommendations based on score
$recommendations = generateRecommendations($totalScore, $categoryScores);

// Save result
$result = FaithGuardRepository::saveQuizResult([
    'user_id' => $user['user_id'],
    'total_score' => $totalScore,
    'category_scores' => $categoryScores,
    'recommendations' => $recommendations
]);

successResponse([
    'score' => $totalScore,
    'max_score' => count($answers) * 4,
    'category_scores' => $categoryScores,
    'recommendations' => $recommendations
]);

/**
 * Generate recommendations based on quiz results
 */
function generateRecommendations(int $score, array $categoryScores): string {
    $recommendations = [];
    
    // Overall score interpretation
    if ($score <= 5) {
        $recommendations[] = "Your digital habits appear healthy. Continue maintaining balance in your life.";
    } elseif ($score <= 10) {
        $recommendations[] = "You're showing some signs of digital dependency. Consider setting boundaries around device usage.";
    } elseif ($score <= 15) {
        $recommendations[] = "Your quiz results suggest moderate digital addiction patterns. We recommend exploring our resources on digital fasting and mindfulness.";
    } else {
        $recommendations[] = "Your results indicate significant digital addiction concerns. Please consider speaking with a counselor or pastor, and explore our recovery resources.";
    }
    
    // Category-specific recommendations
    if (isset($categoryScores['dependence']) && $categoryScores['dependence'] >= 6) {
        $recommendations[] = "Focus on building healthy coping mechanisms for anxiety without digital devices.";
    }
    
    if (isset($categoryScores['spiritual']) && $categoryScores['spiritual'] >= 6) {
        $recommendations[] = "Prioritize daily prayer and Scripture reading, even if briefly, to strengthen your spiritual foundation.";
    }
    
    if (isset($categoryScores['health']) && $categoryScores['health'] >= 6) {
        $recommendations[] = "Consider establishing a digital curfew to improve sleep quality.";
    }
    
    return implode(" ", $recommendations);
}
