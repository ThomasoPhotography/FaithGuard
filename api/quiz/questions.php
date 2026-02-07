<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

if (!ENABLE_QUIZ) {
    errorResponse('Quiz is currently disabled', 403);
}

$questions = FaithGuardRepository::getQuizQuestions();

jsonResponse($questions);