<?php
declare(strict_types=1);

require_once __DIR__ . '/../services/bibleApiService.php';
require_once __DIR__ . '/../../db/FaithGuardRepository.php';
//require_once __DIR__ . '/../services/ScriptureLanguageResolver.php';

$siteLanguage = $_SESSION['site_language'] ?? 'en';
//$default      = ScriptureLanguageResolver::getDefaultTranslation($siteLanguage);

$bibleId      = $_GET['bibleId']     ?? $default['bibleId'];
$translation  = $_GET['translation'] ?? $default['translation'];

$result = FaithGuardRepository::resolveVerse(
    $bibleId,
    $verseKey,
    $translation
);

if (!$result) {
    echo '<div class="alert alert-warning">Scripture could not be loaded.</div>';
    exit;
}
?>

<div class="scripture-modal">
    <p class="fw-bold mb-1"><?= htmlspecialchars($verseKey) ?> (<?= htmlspecialchars($translation) ?>)</p>
    <blockquote class="blockquote">
        <?= nl2br(htmlspecialchars($result['text'])) ?>
    </blockquote>

    <p class="small text-muted mt-2">
        Source: <?= $result['source'] === 'cache' ? 'Saved Scripture' : 'Bible API' ?>
    </p>
</div>