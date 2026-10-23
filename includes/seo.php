<?php
declare(strict_types=1);

/**
 * Shared page helpers: escaping, base URL, <head> output, breadcrumbs.
 * Include with require_once __DIR__ . '/includes/seo.php'.
 */

const FG_SITE_NAME = 'FaithGuard';
const FG_LOCALE = 'en_GB';
const FG_POLICIES_UPDATED = '22 October 2026';

function fg_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function fg_env(string $name): string
{
    $value = getenv($name);
    return is_string($value) ? trim($value) : '';
}

/**
 * Public base URL without trailing slash.
 * Order: FAITHGUARD_BASE_URL, the WEB_URL constant from db/config.php, then the request host.
 */
function fg_base_url(): string
{
    $configured = fg_env('FAITHGUARD_BASE_URL');
    if ($configured === '' && defined('WEB_URL')) {
        $configured = trim((string) constant('WEB_URL'));
    }
    if ($configured !== '') {
        return rtrim($configured, '/');
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    if (!preg_match('/^[A-Za-z0-9.\-]+(:\d+)?$/', $host)) {
        $host = 'localhost';
    }
    return ($https ? 'https' : 'http') . '://' . $host;
}

function fg_abs_url(string $path): string
{
    return fg_base_url() . $path;
}

/** Search engines are only invited in once FAITHGUARD_INDEXING=on is set on the server. */
function fg_indexing_enabled(): bool
{
    return fg_env('FAITHGUARD_INDEXING') === 'on';
}

function fg_contact_email(): string
{
    $email = fg_env('FAITHGUARD_CONTACT_EMAIL');
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
}

function fg_operator(): string
{
    return fg_env('FAITHGUARD_OPERATOR');
}

/** Asset URL with a version taken from the file's modification time. */
function fg_asset(string $path): string
{
    $file = __DIR__ . '/../' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '0';
    return '/' . ltrim($path, '/') . '?v=' . $version;
}

function fg_aria_current(string $path, array $meta): string
{
    return $path === ($meta['path'] ?? '') ? ' aria-current="page"' : '';
}

/**
 * Prints everything from <!DOCTYPE> to the opening <body> tag and the skip link.
 * $meta keys: title, description, path, breadcrumbs (list of [label, path|null]), noindex (bool).
 */
function fg_render_head(array $meta): void
{
    $meta += ['noindex' => false, 'breadcrumbs' => []];
    $base = fg_base_url();
    $url = fg_abs_url($meta['path']);
    $image = fg_abs_url('/assets/uploads/og-default.png');
    $imageAlt = 'FaithGuard shield logo with a cross and flame, beside the name FaithGuard and the line Scripture, prayer and community for a quieter life online.';
    $blocked = $meta['noindex'] || !fg_indexing_enabled();

    $graph = [
        [
            '@type' => 'Organization',
            '@id' => $base . '/#organization',
            'name' => FG_SITE_NAME,
            'url' => $base . '/',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => fg_abs_url('/assets/uploads/icon-512.png'),
                'width' => 512,
                'height' => 512,
            ],
        ],
        [
            '@type' => 'WebSite',
            '@id' => $base . '/#website',
            'url' => $base . '/',
            'name' => FG_SITE_NAME,
            'inLanguage' => 'en',
            'publisher' => ['@id' => $base . '/#organization'],
        ],
    ];

    if (!$meta['noindex']) {
        $webPage = [
            '@type' => 'WebPage',
            '@id' => $url . '#webpage',
            'url' => $url,
            'name' => $meta['title'],
            'description' => $meta['description'],
            'inLanguage' => 'en',
            'isPartOf' => ['@id' => $base . '/#website'],
        ];
        $graph[] = &$webPage;
        if (count($meta['breadcrumbs']) > 1) {
            $items = [];
            foreach ($meta['breadcrumbs'] as $i => [$label, $path]) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $label,
                    'item' => $path === null ? $url : fg_abs_url($path),
                ];
            }
            $webPage['breadcrumb'] = ['@id' => $url . '#breadcrumb'];
            $graph[] = [
                '@type' => 'BreadcrumbList',
                '@id' => $url . '#breadcrumb',
                'itemListElement' => $items,
            ];
        }
        unset($webPage);
    }

    $jsonLd = json_encode(
        ['@context' => 'https://schema.org', '@graph' => $graph],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR
    );
    $title = fg_e($meta['title']);
    $description = fg_e($meta['description']);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <meta name="description" content="<?= $description ?>">
<?php if ($blocked): ?>
    <meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<?php if (!$meta['noindex']): ?>
    <link rel="canonical" href="<?= fg_e($url) ?>">
<?php endif; ?>
    <meta name="theme-color" content="#8b5a2b">

    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" href="/assets/uploads/FaithGuard_Primary_Logo.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/assets/uploads/apple-touch-icon.png">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= fg_e(FG_SITE_NAME) ?>">
    <meta property="og:locale" content="<?= fg_e(FG_LOCALE) ?>">
    <meta property="og:title" content="<?= $title ?>">
    <meta property="og:description" content="<?= $description ?>">
    <meta property="og:url" content="<?= fg_e($url) ?>">
    <meta property="og:image" content="<?= fg_e($image) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?= fg_e($imageAlt) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $title ?>">
    <meta name="twitter:description" content="<?= $description ?>">
    <meta name="twitter:image" content="<?= fg_e($image) ?>">
    <meta name="twitter:image:alt" content="<?= fg_e($imageAlt) ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= fg_e(fg_asset('assets/css/main.css')) ?>">
    <script type="application/ld+json"><?= $jsonLd ?></script>
</head>
<body>
<a class="c-skip-link" href="#main">Skip to main content</a>
<?php
}

function fg_render_breadcrumbs(array $meta): void
{
    $crumbs = $meta['breadcrumbs'] ?? [];
    if (count($crumbs) < 2) {
        return;
    }
    $last = count($crumbs) - 1;
    echo '<nav class="c-breadcrumbs container" aria-label="Breadcrumb"><ol class="c-breadcrumbs__list">';
    foreach ($crumbs as $i => [$label, $path]) {
        echo '<li class="c-breadcrumbs__item">';
        if ($i === $last) {
            echo '<span aria-current="page">' . fg_e($label) . '</span>';
        } else {
            echo '<a class="c-breadcrumbs__link" href="' . fg_e($path) . '">' . fg_e($label) . '</a>';
        }
        echo '</li>';
    }
    echo '</ol></nav>' . "\n";
}