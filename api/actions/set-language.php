<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../db/FaithGuardRepository.php';

$allowed = ['en', 'nl'];
$lang    = $_POST['language'] ?? 'en';

if (!in_array($lang, $allowed, true)) {
    $lang = 'en';
}

/* Persist to session */
$_SESSION['language'] = $lang;

/* Persist to user profile if logged in */
if (!empty($_SESSION['user_id'])) {
    FaithGuardRepository::updateUserLanguage(
        (int) $_SESSION['user_id'],
        $lang
    );
}

/* Return safely */
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;
