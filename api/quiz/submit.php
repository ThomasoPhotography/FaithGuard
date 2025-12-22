<?php

declare (strict_types = 1);

require_once __DIR__ . "/../../db/database.php";
require_once __DIR__ . "/../../db/FaithGuardRepository.php";
require_once __DIR__ . "/../helper/debug.php";

session_start();
header('Content-Type: application/json');

/* ============================
   AUTH & METHOD GUARD
============================ */

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

/* ============================
   CONFIGURATION
============================ */

$CATEGORY_WEIGHTS = [
    'secrecy'       => 1.3,
    'guilt'         => 1.3,
    'compulsion'    => 1.4,
    'isolation'     => 1.2,
    'risk'          => 1.2,
    'health'        => 1.1,
    'spiritual'     => 1.4,
    'relationships' => 1.2,
    'control'       => 1.3,
];

$ADDICTION_WEIGHTS = [
    'pornography' => [
        'secrecy'   => 1.4,
        'guilt'     => 1.4,
        'spiritual' => 1.5,
    ],
    'alcohol'     => [
        'health'  => 1.4,
        'risk'    => 1.4,
        'control' => 1.3,
    ],
    'drugs'       => [
        'health'    => 1.5,
        'risk'      => 1.4,
        'isolation' => 1.3,
    ],
    'gambling'    => [
        'control'       => 1.4,
        'risk'          => 1.5,
        'relationships' => 1.3,
    ],
];

$SCRIPTURE_MAP = [
    'pornography' => [
        'Low Risk'          => ['1 Corinthians 10:13'],
        'Mild Concern'      => ['Psalm 119:9'],
        'Moderate Struggle' => ['Matthew 5:28', 'Romans 12:2'],
        'Severe Struggle'   => ['Job 31:1'],
        'Critical'          => ['Psalm 51:10', '1 John 1:7'],
    ],
    'alcohol'     => [
        'Low Risk'          => ['1 Corinthians 6:12'],
        'Moderate Struggle' => ['Proverbs 20:1'],
        'Severe Struggle'   => ['Ephesians 5:18'],
        'Critical'          => ['Romans 13:13'],
    ],
];

/* ============================
   HELPERS
============================ */

function interpretScore(int $score): string
{
    return match (true) {
        $score <= 25 => 'Low Risk',
        $score <= 45 => 'Mild Concern',
        $score <= 65 => 'Moderate Struggle',
        $score <= 80 => 'Severe Struggle',
        default      => 'Critical',
    };
}

function fetchBibleVerse(string $reference): ?array
{
    $apiKey = '9HmOm_ZxzdLKebUeeWl8p';
    $url    = "https://api.scripture.api.bible/v1/bibles/de4e12af7f28f599-01/search?query=" . urlencode($reference);

    $context = stream_context_create([
        'http'    => [
            'header' => "api-key: {$apiKey}\r\n",
            'timeout' => 4,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    return $data['data']['verses'][0] ?? null;
}

/* ============================
   INPUT VALIDATION
============================ */

$payload = json_decode(file_get_contents('php://input'), true);

if (! is_array($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Malformed JSON']);
    exit;
}

if (
    empty($payload['addiction_types']) ||
    ! is_array($payload['addiction_types']) ||
    empty($payload['answers']) ||
    ! is_array($payload['answers'])
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid payload structure']);
    exit;
}

/* ============================
   SANITIZE ADDICTIONS
============================ */

$validAddictions = array_keys($ADDICTION_WEIGHTS);

$addictionTypes = array_values(array_intersect(
    $validAddictions,
    array_map('strval', $payload['addiction_types'])
));

if (empty($addictionTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No valid addiction types selected']);
    exit;
}

/* ============================
   SCORING
============================ */

$questions = FaithGuardRepository::getAllQuizQuestions();
$answers   = $payload['answers'];

$totalWeighted     = 0.0;
$normalizedAnswers = [];

foreach ($questions as $q) {
    $qid = (int) $q['id'];

    if (! isset($answers[$qid])) {
        continue;
    }

    $value = (int) $answers[$qid];
    if ($value < 1 || $value > 5) {
        continue;
    }

    $category  = $q['category'] ?? null;
    $base      = $value;
    $catWeight = $CATEGORY_WEIGHTS[$category] ?? 1.0;

    $addWeight = 1.0;
    foreach ($addictionTypes as $type) {
        if (isset($ADDICTION_WEIGHTS[$type][$category])) {
            $addWeight *= $ADDICTION_WEIGHTS[$type][$category];
        }
    }

    // Prevent runaway multipliers
    $addWeight = min($addWeight, 2.5);

    $totalWeighted += $base * $catWeight * $addWeight;
    $normalizedAnswers[$qid] = $value;
}

$maxScore   = count($questions) * 5 * 1.5;
$percentage = (int) round(($totalWeighted / $maxScore) * 100);
$percentage = max(0, min(100, $percentage));

$level = interpretScore($percentage);

/* ============================
   SCRIPTURE RESOLUTION
============================ */

$scriptureRefs = [];

foreach ($addictionTypes as $type) {
    if (! empty($SCRIPTURE_MAP[$type][$level])) {
        $scriptureRefs = array_merge(
            $scriptureRefs,
            $SCRIPTURE_MAP[$type][$level]
        );
    }
}

$scriptureRefs = array_unique($scriptureRefs);
$scriptures    = [];

foreach ($scriptureRefs as $ref) {
    $verse = fetchBibleVerse($ref);
    if ($verse) {
        $scriptures[] = [
            'reference' => $ref,
            'text'      => $verse['text'] ?? '',
        ];
    }
}

/* ============================
   PERSIST RESULT
============================ */

FaithGuardRepository::createQuizResult(
    $_SESSION['user_id'],
    json_encode($addictionTypes, JSON_THROW_ON_ERROR),
    json_encode($normalizedAnswers, JSON_THROW_ON_ERROR),
    $percentage
);

/* ============================
   RESPONSE
============================ */

echo json_encode([
    'success'       => true,
    'score'         => $percentage,
    'level'         => $level,
    'addictions'    => $addictionTypes,
    'scripture'     => $scriptures,
    'resource_tags' => array_merge($addictionTypes, [$level]),
]);
