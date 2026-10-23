<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/seo.php';

header('Content-Type: text/plain; charset=utf-8');

if (!fg_indexing_enabled()) {
    // Before launch: ask crawlers to stay out. Set FAITHGUARD_INDEXING=on to open the site.
    echo "User-agent: *\nDisallow: /\n";
    return;
}

echo "User-agent: *\n";
echo "Allow: /\n";
foreach (['/api/', '/db/', '/admin/', '/includes/', '/partials/', '/dashboard.php'] as $path) {
    echo 'Disallow: ' . $path . "\n";
}
echo "\nSitemap: " . fg_abs_url('/sitemap.xml') . "\n";