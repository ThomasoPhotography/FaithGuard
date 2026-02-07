<?php
require_once __DIR__ . '/../base.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

// Get parameters
$reference = $_GET['reference'] ?? '2CO.5.7'; // Default to 2 Corinthians 5:7
$lang      = $_GET['lang'] ?? 'en';

// Validate language
if (! in_array($lang, ['en', 'nl'])) {
    $lang = 'en';
}

// Define constants for Bible API
define('BIBLE_VERSION_ID_EN', 'de4e12af7f28f599-02'); // NRSVUE
define('BIBLE_VERSION_ID_NL', '06125adad2d5898-01');  // NBV21
define('BIBLE_VERSION_EN', 'NRSVUE');
define('BIBLE_VERSION_NL', 'NBV21');

// Select Bible version based on language
$bibleId     = ($lang === 'nl') ? 'BIBLE_VERSION_ID_NL' : 'BIBLE_VERSION_ID_EN';
$versionName = ($lang === 'nl') ? 'BIBLE_VERSION_NL' : 'BIBLE_VERSION_EN';

// Build API URL
$apiUrl = sprintf(
    '%s/bibles/%s/passages/%s',
    'BIBLE_API_BASE_URL',
    $bibleId,
    urlencode($reference)
);

// Set up HTTP context with headers (no cURL)
$contextOptions = [
    'http' => [
        'method'        => 'GET',
        'header'        => [
            'api-key: ' . BIBLE_API_KEY,
            'Accept: application/json',
        ],
        'timeout'       => 10,
        'ignore_errors' => true,
    ],
    'ssl'  => [
        'verify_peer'      => true,
        'verify_peer_name' => true,
    ],
];

$context = stream_context_create($contextOptions);

// Make request using file_get_contents (no cURL)
$response = @file_get_contents($apiUrl, false, $context);

if ($response === false) {
    // Fallback: return the verse from local cache/database
    $fallbackVerse = getFallbackVerse($reference, $lang);
    if ($fallbackVerse) {
        successResponse([
            'verse'  => $fallbackVerse,
            'cached' => true,
        ]);
    }
    errorResponse('Unable to fetch verse. Please try again later.', 503);
}

// Parse response
$data = json_decode($response, true);

if (empty($data) || ! isset($data['data'])) {
    // Try fallback
    $fallbackVerse = getFallbackVerse($reference, $lang);
    if ($fallbackVerse) {
        successResponse([
            'verse'  => $fallbackVerse,
            'cached' => true,
        ]);
    }
    errorResponse('Invalid response from Bible API', 502);
}

$passage = $data['data'];

// Extract verse text
$verseText = '';
if (isset($passage['content'])) {
    // Clean up HTML tags from content
    $verseText = strip_tags($passage['content']);
    $verseText = html_entity_decode($verseText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Normalize whitespace
    $verseText = preg_replace('/\s+/', ' ', $verseText);
    $verseText = trim($verseText);
} elseif (isset($passage['text'])) {
    $verseText = $passage['text'];
}

// Build reference display
$referenceDisplay = $passage['reference'] ?? formatReference($reference);

successResponse([
    'verse' => [
        'reference' => $referenceDisplay,
        'text'      => $verseText,
        'version'   => $versionName,
        'language'  => $lang,
    ],
]);

function getFallbackVerse(string $reference, string $lang): ?array
{
    $fallbacks = [
        '2CO.5.7'   => [
            'en' => [
                'reference' => '2 Corinthians 5:7',
                'text'      => 'for we walk by faith, not by sight.',
                'version'   => 'NRSVUE',
                'language'  => 'en',
            ],
            'nl' => [
                'reference' => '2 Korintiërs 5:7',
                'text'      => 'want wij leven bij geloof, niet bij wat wij zien.',
                'version'   => 'NBV21',
                'language'  => 'nl',
            ],
        ],
        'JHN.3.16'  => [
            'en' => [
                'reference' => 'John 3:16',
                'text'      => 'For God so loved the world that he gave his only Son, so that everyone who believes in him may not perish but may have eternal life.',
                'version'   => 'NRSVUE',
                'language'  => 'en',
            ],
            'nl' => [
                'reference' => 'Johannes 3:16',
                'text'      => 'Want God had de wereld zo lief dat hij zijn enige Zoon heeft gegeven, opdat iedereen die in hem gelooft niet verloren gaat, maar eeuwig leven heeft.',
                'version'   => 'NBV21',
                'language'  => 'nl',
            ],
        ],
        'PHP.4.13'  => [
            'en' => [
                'reference' => 'Philippians 4:13',
                'text'      => 'I can do all things through him who strengthens me.',
                'version'   => 'NRSVUE',
                'language'  => 'en',
            ],
            'nl' => [
                'reference' => 'Filippenzen 4:13',
                'text'      => 'Ik vermag alle dingen door hem die mij kracht geeft.',
                'version'   => 'NBV21',
                'language'  => 'nl',
            ],
        ],
        'PSA.23.1'  => [
            'en' => [
                'reference' => 'Psalm 23:1',
                'text'      => 'The Lord is my shepherd, I shall not want.',
                'version'   => 'NRSVUE',
                'language'  => 'en',
            ],
            'nl' => [
                'reference' => 'Psalm 23:1',
                'text'      => 'De Heer is mijn herder, het ontbreekt mij aan niets.',
                'version'   => 'NBV21',
                'language'  => 'nl',
            ],
        ],
        'ISA.41.10' => [
            'en' => [
                'reference' => 'Isaiah 41:10',
                'text'      => 'Do not fear, for I am with you; do not be afraid, for I am your God; I will strengthen you, I will help you, I will uphold you with my victorious right hand.',
                'version'   => 'NRSVUE',
                'language'  => 'en',
            ],
            'nl' => [
                'reference' => 'Jesaja 41:10',
                'text'      => 'Wees niet bevreesd, want ik ben met je; wees niet angstig, want ik ben je God. Ik versterk je, ik help je, ik ondersteun je met mijn overwinnaarshand.',
                'version'   => 'NBV21',
                'language'  => 'nl',
            ],
        ],
    ];

    return $fallbacks[$reference][$lang] ?? null;
}

function formatReference(string $ref): string
{
    $bookNames = [
        'GEN' => 'Genesis', 'EXO'         => 'Exodus', 'LEV'          => 'Leviticus', 'NUM'     => 'Numbers',
        'DEU' => 'Deuteronomy', 'JOS'     => 'Joshua', 'JDG'          => 'Judges', 'RUT'        => 'Ruth',
        '1SA' => '1 Samuel', '2SA'        => '2 Samuel', '1KI'        => '1 Kings', '2KI'       => '2 Kings',
        '1CH' => '1 Chronicles', '2CH'    => '2 Chronicles', 'EZR'    => 'Ezra', 'NEH'          => 'Nehemiah',
        'EST' => 'Esther', 'JOB'          => 'Job', 'PSA'             => 'Psalm', 'PRO'         => 'Proverbs',
        'ECC' => 'Ecclesiastes', 'SNG'    => 'Song of Solomon', 'ISA' => 'Isaiah', 'JER'        => 'Jeremiah',
        'LAM' => 'Lamentations', 'EZK'    => 'Ezekiel', 'DAN'         => 'Daniel', 'HOS'        => 'Hosea',
        'JOL' => 'Joel', 'AMO'            => 'Amos', 'OBA'            => 'Obadiah', 'JON'       => 'Jonah',
        'MIC' => 'Micah', 'NAM'           => 'Nahum', 'HAB'           => 'Habakkuk', 'ZEP'      => 'Zephaniah',
        'HAG' => 'Haggai', 'ZEC'          => 'Zechariah', 'MAL'       => 'Malachi',
        'MAT' => 'Matthew', 'MRK'         => 'Mark', 'LUK'            => 'Luke', 'JHN'          => 'John',
        'ACT' => 'Acts', 'ROM'            => 'Romans', '1CO'          => '1 Corinthians', '2CO' => '2 Corinthians',
        'GAL' => 'Galatians', 'EPH'       => 'Ephesians', 'PHP'       => 'Philippians', 'COL'   => 'Colossians',
        '1TH' => '1 Thessalonians', '2TH' => '2 Thessalonians', '1TI' => '1 Timothy', '2TI'     => '2 Timothy',
        'TIT' => 'Titus', 'PHM'           => 'Philemon', 'HEB'        => 'Hebrews', 'JAS'       => 'James',
        '1PE' => '1 Peter', '2PE'         => '2 Peter', '1JN'         => '1 John', '2JN'        => '2 John',
        '3JN' => '3 John', 'JUD'          => 'Jude', 'REV'            => 'Revelation',
    ];

    // Parse reference format (e.g., "2CO.5.7" or "JHN.3.16")
    $parts = explode('.', $ref);
    if (count($parts) >= 3) {
        $book     = $parts[0];
        $chapter  = $parts[1];
        $verse    = $parts[2];
        $bookName = $bookNames[$book] ?? $book;
        return "$bookName $chapter:$verse";
    }

    return $ref;
}
