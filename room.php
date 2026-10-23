<?php
    declare (strict_types = 1);

    require_once __DIR__ . '/includes/page_context.php';

    $slug  = (string) ($_GET['room'] ?? '');
    $rooms = rooms();

    if (! isset($rooms[$slug])) {
    require __DIR__ . '/404.php';
    exit;
    }

    $room     = $rooms[$slug];
    $meta     = fg_room_meta($slug);
    $slugs    = array_keys($rooms);
    $position = (int) array_search($slug, $slugs, true);
    $previous = $position > 0 ? $rooms[$slugs[$position - 1]] : null;
    $next     = $position < count($slugs) - 1 ? $rooms[$slugs[$position + 1]] : null;

    fg_render_head($meta);
    require __DIR__ . '/partials/nav.php';
?>
<main id="main" class="c-main container my-5">
    <article>
        <h1 class="c-main__title"><?php echo fg_e($room['name']) ?></h1>
        <p class="c-main__subtitle"><?php echo fg_e($room['summary']) ?></p>
        <div class="c-prose">
<?php foreach ($room['body'] as $paragraph): ?>
            <p><?php echo fg_e($paragraph) ?></p>
<?php endforeach; ?>
            <p>New to FaithGuard? Read <a href="/about.php">how it approaches digital addiction</a>, or <a href="/contact.php">ask about this room</a>.</p>
        </div>

        <nav class="c-room__neighbours" aria-label="Neighbouring rooms">
<?php if ($previous !== null): ?>
            <a class="c-room__neighbour" href="/rooms/<?php echo fg_e($previous['slug']) ?>">Previous room: <?php echo fg_e($previous['name']) ?></a>
<?php endif; ?>
<?php if ($next !== null): ?>
            <a class="c-room__neighbour c-room__neighbour--next" href="/rooms/<?php echo fg_e($next['slug']) ?>">Next room: <?php echo fg_e($next['name']) ?></a>
<?php endif; ?>
        </nav>

        <section class="c-room__more" aria-labelledby="room-more-title">
            <h2 class="c-room__more-title" id="room-more-title">All eight rooms</h2>
            <ul class="c-room__more-list">
<?php foreach ($rooms as $other): ?>
<?php if ($other['slug'] !== $slug): ?>
                <li><a href="/rooms/<?php echo fg_e($other['slug']) ?>"><?php echo fg_e($other['name']) ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
            </ul>
        </section>
    </article>
</main>
<?php
    require __DIR__ . '/partials/footer.php';
    require __DIR__ . '/partials/scripts.php';
?>
</body>
</html>