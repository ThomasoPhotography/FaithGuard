<?php
    declare (strict_types = 1);

    require_once __DIR__ . '/includes/page_context.php';

    http_response_code(404);
    header('X-Robots-Tag: noindex');

    $meta                = fg_private_meta('Page not found', '/404.php');
    $meta['description'] = 'The page you asked for does not exist or has moved.';
    fg_render_head($meta);
    require __DIR__ . '/partials/nav.php';
?>
<main id="main" class="c-main container my-5">
    <h1 class="c-main__title">Page not found</h1>
    <p class="c-main__subtitle">The page you asked for does not exist or has moved.</p>
    <div class="c-prose">
        <p>Try one of these instead:</p>
        <ul>
            <li><a href="/index.php">Go to the home page</a></li>
            <li><a href="/rooms/scripture">Start at the Scripture room</a></li>
            <li><a href="/resources.php">Browse the resources</a></li>
            <li><a href="/contact.php">Contact us</a> if a link on our site is broken</li>
        </ul>
    </div>
</main>
<?php
    require __DIR__ . '/partials/footer.php';
    require __DIR__ . '/partials/scripts.php';
?>
</body>
</html>