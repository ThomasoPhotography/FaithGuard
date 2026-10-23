<?php
declare (strict_types = 1);

/** Old policy URLs (policies.php?slug=terms) now redirect permanently to the new pages. */
$targets = ['terms' => '/terms.php', 'privacy' => '/privacy.php', 'cookie' => '/cookies.php'];
$slug    = (string) ($_GET['slug'] ?? '');

if (isset($targets[$slug])) {
    header('Location: ' . $targets[$slug], true, 301);
    exit;
}

http_response_code(404);
require __DIR__ . '/404.php';
