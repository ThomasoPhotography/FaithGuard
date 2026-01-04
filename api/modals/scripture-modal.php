<?php
    declare (strict_types = 1);

    /* =========================================================
   SESSION
========================================================= */
    session_start();

    /* =========================================================
   CORE REQUIREMENTS
========================================================= */
    require_once __DIR__ . '/../services/languageService.php';
    require_once __DIR__ . '/../services/bibleApiService.php';
    require_once __DIR__ . '/../../db/FaithGuardRepository.php';

    /* =========================================================
   INPUT GUARD
========================================================= */
    $verseKey = $_GET['verse'] ?? null;

    if (! $verseKey) {
        echo '<p class="text-muted">No scripture selected.</p>';
        exit;
    }

    /* =========================================================
   LANGUAGE & TRANSLATION
========================================================= */
    $language    = LanguageService::getCurrentLanguage();
    $translation = LanguageService::getBibleTranslation();

    /* =========================================================
   SCRIPTURE RESOLUTION (CACHE → API)
========================================================= */
    $result = FaithGuardRepository::resolveVerse(
        $translation,
        $verseKey,
        $bibleId,
    );

    if (! $result || empty($result['text'])) {
        if ($language = 'en') {
            echo '<div class="alert alert-warning">Scripture could not be loaded.</div>';
            exit;
        } else if ($language = 'nl') {
            echo '<div class="alert alert-warning">Het Heilig Shrift kon niet geladen worden.</div>';
            exit;
        } else {
            echo '<div class="alert alert-warning">Faithguard error 1: Language not found.</div>';
            exit;
        }
    }
?>

<div class="c-scripture-modal">
    <p class="fw-bold mb-1">
        <?php echo htmlspecialchars($verseKey) ?>
        <span class="text-muted small">(<?php echo htmlspecialchars($translation) ?>)</span>
    </p>

    <blockquote class="blockquote">
        <?php echo nl2br(htmlspecialchars($result['text'])) ?>
    </blockquote>

    <p class="small text-muted mt-2">
        Source:                <?php echo $result['source'] === 'cache' ? 'Saved Scripture' : 'Bible API' ?>
    </p>

    <?php if ($language === 'nl'): ?>
        <p class="small text-muted mt-3">
            Schriftcitaten zijn afkomstig uit de NBV21.
            Tekst kan afwijken van andere vertalingen.
        </p>
    <?php else: ?>
        <p class="small text-muted mt-3">
            Scripture quotations are from the NRSVUE.
            Text may differ from other translations.
        </p>
    <?php endif; ?>
</div>
