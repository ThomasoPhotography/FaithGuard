<?php

require_once __DIR__ . "/../../db/database.php";
require_once __DIR__ . "/../../db/FaithGuardRepository.php";
require_once __DIR__ . "/../helper/debug.php";

session_start();
header('Content-Type: application/json');

if (! isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
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

function interpretScore(int $score): array
{
    return match (true) {
        $score <= 25 => ['level' => 'Low Risk', 'tone' => 'success'],
        $score <= 45 => ['level' => 'Mild Concern', 'tone' => 'info'],
        $score <= 65 => ['level' => 'Moderate Struggle', 'tone' => 'warning'],
        $score <= 80 => ['level' => 'Severe Struggle', 'tone' => 'danger'],
        default      => ['level' => 'Critical', 'tone' => 'danger'],
    };
}

function fetchBibleVerse(string $reference): ?array
{
    $apiKey = '9HmOm_ZxzdLKebUeeWl8p';
    $url    = "https://api.scripture.api.bible/v1/bibles/de4e12af7f28f599-01/search?query=" . urlencode($reference);

    $context = stream_context_create([
        'http'    => [
            'header' => "api-key: {$apiKey}\r\n",
            'timeout' => 5,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if (! $response) {
        return null;
    }

    $data = json_decode($response, true);
    return $data['data']['verses'][0] ?? null;
}

/* ============================
   PROCESS INPUT
============================ */

$payload = json_decode(file_get_contents('php://input'), true);

if (
    ! isset($payload['addiction_type']) ||
    ! isset($payload['answers']) ||
    ! is_array($payload['answers'])
) {
    throw new Exception('Invalid payload');
}

$addictionType = $payload['addiction_type'];
$answers       = $payload['answers'];

$questions = FaithGuardRepository::getAllQuizQuestions();

$totalWeighted     = 0;
$normalizedAnswers = [];

foreach ($questions as $q) {
    $qid = $q['id'];
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
    $addWeight = $ADDICTION_WEIGHTS[$addictionType][$category] ?? 1.0;

    $totalWeighted += $base * $catWeight * $addWeight;
    $normalizedAnswers[$qid] = $value;
}

$maxScore   = count($questions) * 5 * 1.5;
$percentage = round(($totalWeighted / $maxScore) * 100);

$interpretation = interpretScore($percentage);

/* ============================
   SCRIPTURE + RESOURCES
============================ */

$scriptureRefs = $SCRIPTURE_MAP[$addictionType][$interpretation['level']] ?? [];
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
   SAVE RESULT
============================ */

FaithGuardRepository::createQuizResult(
    $_SESSION['user_id'],
    $addictionType,
    json_encode($normalizedAnswers),
    $percentage
);

/* ============================
   RESPONSE
============================ */

echo json_encode([
    'success'       => true,
    'score'         => $percentage,
    'level'         => $interpretation['level'],
    'scripture'     => $scriptures,
    'resource_tags' => [$addictionType, $interpretation['level']],
]);
