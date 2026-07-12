<?php
require_once __DIR__ . '/../base.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

$sessionId = $_COOKIE[SESSION_COOKIE_NAME] ?? null;

if ($sessionId) {
    FaithGuardRepository::deleteSession($sessionId);
    clearSessionCookie();
}

startAppSession();
$_SESSION = [];
session_destroy();

successResponse();
