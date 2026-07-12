<?php
require __DIR__ . '/../api/base.php';

function expectCondition(bool $condition, string $message): void
{
    if (! $condition) {
        throw new Exception($message);
    }
}

startAppSession();
$_SERVER['HTTP_X_CSRF_TOKEN'] = 'header-token';
expectCondition(getCsrfTokenFromRequest([]) === 'header-token', 'Header-based CSRF token should be accepted');

$_SERVER['HTTP_X_CSRF_TOKEN'] = '';
expectCondition(getCsrfTokenFromRequest(['csrf_token' => 'body-token']) === 'body-token', 'Body-based CSRF token should be accepted');

$_SESSION['csrf_token'] = 'session-token';
expectCondition(ensureCsrfToken() === 'session-token', 'Session token should be returned when present');

echo "register csrf tests passed\n";
