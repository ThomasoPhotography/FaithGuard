<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

$user  = requireAuth();
$input = getJsonInput();

$moodRating = (int) ($input['mood_rating'] ?? 0);
$notes      = isset($input['notes']) ? sanitize($input['notes']) : null;
$triggers   = isset($input['triggers']) ? sanitize($input['triggers']) : null;
$victories  = isset($input['victories']) ? sanitize($input['victories']) : null;

// Validate mood rating
if ($moodRating < 1 || $moodRating > 5) {
    errorResponse('Mood rating must be between 1 and 5');
}

$today = date('Y-m-d');

// Check if already checked in today
$existing = FaithGuardRepository::getCheckinByDate($user['user_id'], $today);
if ($existing) {
    errorResponse('Already checked in today');
}

// Create check-in
$result = FaithGuardRepository::createCheckin([
    'user_id'      => $user['user_id'],
    'checkin_date' => $today,
    'mood_rating'  => $moodRating,
    'notes'        => $notes,
    'triggers'     => $triggers,
    'victories'    => $victories,
]);

if (! $result) {
    errorResponse('Failed to create check-in', 500);
}

// Update progress
$progress = FaithGuardRepository::getUserProgress($user['user_id']);
if ($progress) {
    $streakDays    = $progress['streak_days'];
    $longestStreak = $progress['longest_streak'];
    $totalCheckins = $progress['total_checkins'];
    $lastCheckin   = $progress['last_checkin'];

    // Calculate streak
    if ($lastCheckin) {
        $lastDate  = new DateTime($lastCheckin);
        $todayDate = new DateTime($today);
        $diff      = $lastDate->diff($todayDate)->days;

        if ($diff === 1) {
            // Consecutive day
            $streakDays++;
        } elseif ($diff > 1) {
            // Streak broken
            $streakDays = 1;
        }
    } else {
        $streakDays = 1;
    }

    // Update longest streak
    if ($streakDays > $longestStreak) {
        $longestStreak = $streakDays;
    }

    FaithGuardRepository::updateUserProgress($user['user_id'], [
        'streak_days'    => $streakDays,
        'longest_streak' => $longestStreak,
        'total_checkins' => $totalCheckins + 1,
        'last_checkin'   => $today,
    ]);
}

successResponse([
    'streak_days'  => $streakDays ?? 1,
    'checkin_date' => $today,
]);
