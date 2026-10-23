<?php
declare (strict_types = 1);

require_once __DIR__ . '/includes/seo.php';
require_once __DIR__ . '/includes/rooms.php';
require_once __DIR__ . '/includes/pages.php';

header('Content-Type: application/xml; charset=utf-8');

$urls = [];
foreach (fg_pages() as $page) {
    $urls[] = $page['path'];
}
foreach (rooms() as $room) {
    $urls[] = '/rooms/' . $room['slug'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $path) {
    echo '  <url><loc>' . htmlspecialchars(fg_abs_url($path), ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc></url>' . "\n";
}
echo '</urlset>' . "\n";
