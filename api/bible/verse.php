<?php
require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db/FaithGuardRepository.php';

// Validate input
$verse = $_GET['verse'] ?? '';
$lang  = $_GET['lang'] ?? 'NRSVUE'; // default fallback

if (empty($verse)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing verse parameter']);
    exit;
}

// Build API‑Bible URL
$endpoint = sprintf(
    'https://api.bible/v1/bibles/%s/verses/%s',
    rawurlencode($lang),
    rawurlencode($verse)
);

// Prepare stream context with Authorization header
$apiKey = defined('BIBLE_API_KEY') ? BIBLE_API_KEY : getenv('BIBLE_API_KEY');
if (!$apiKey) {
    http_response_code(500);
    echo json_encode(['error' => 'BIBLE_API_KEY not configured']);
    exit;
}

$opts = [
    'http' => [
        'method'  => 'GET',
        'header'  => "Authorization: Bearer " . $apiKey,
        'timeout' => 8,
    ],
];
$context = stream_context_create($opts);

// Perform request without curl
$response = @file_get_contents($endpoint, false, $context);

if ($response === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to contact Bible API']);
    exit;
}

// Forward API‑Bible JSON directly (no envelope)
header('Content-Type: application/json');
echo $response;
