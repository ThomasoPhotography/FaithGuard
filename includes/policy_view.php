<?php
/**
 * Renders a policy page. The including page sets $policyKey ('terms', 'privacy' or 'cookies')
 * and $dbSlug ('terms', 'privacy' or 'cookie', the slug in the policy table).
 * Text saved by an administrator wins; until then the default text in partials/policies/ is shown.
 */
require_once __DIR__ . '/page_context.php';

$meta = fg_page_meta($policyKey);
$policy = null;
try {
    $policy = FaithGuardRepository::getPolicyContent($dbSlug);
} catch (Throwable $error) {
    error_log('FaithGuard policy load: ' . $error->getMessage());
}

$dbText = trim((string) ($policy['content_text'] ?? ''));
$useDatabaseText = $dbText !== '' && strcasecmp($dbText, 'To be completed.') !== 0;
$updated = $useDatabaseText && !empty($policy['updated_at'])
    ? date('j F Y', (int) strtotime((string) $policy['updated_at']))
    : FG_POLICIES_UPDATED;
$operator = fg_operator();

fg_render_head($meta);
require __DIR__ . '/../partials/nav.php';
?>
<main id="main" class="c-main container my-5">
    <article>
        <h1 class="c-main__title"><?= fg_e($meta['label']) ?></h1>
        <p class="c-main__subtitle">Last updated <?= fg_e($updated) ?></p>
        <div class="c-prose">
<?php if ($useDatabaseText): ?>
            <?= nl2br(fg_e($dbText)) ?>
<?php else: ?>
<?php require __DIR__ . '/../partials/policies/' . $policyKey . '.php'; ?>
<?php endif; ?>
        </div>
        <a href="/" class="btn c-btn c-btn__dashboard mt-4">Back to home</a>
    </article>
</main>
<?php
require __DIR__ . '/../partials/footer.php';
require __DIR__ . '/../partials/scripts.php';
?>
</body>
</html>