<?php
    declare (strict_types = 1);

    require_once __DIR__ . '/includes/page_context.php';

    $meta = fg_page_meta('about');
    fg_render_head($meta);
    require __DIR__ . '/partials/nav.php';
?>
<main id="main" class="c-main container my-5">
    <article>
        <h1 class="c-main__title">About FaithGuard</h1>
        <p class="c-main__subtitle">FaithGuard is a Catholic digital wellbeing platform for people who want a healthier relationship with their phone, their feeds and their screens.</p>
        <div class="c-prose">
            <h2>Why it exists</h2>
            <p>Digital addiction is usually treated as a habit to track. FaithGuard treats it as a spiritual and practical struggle, and answers it with Scripture, accountability, prayer and community.</p>

            <h2>How it works</h2>
            <ul>
                <li><strong>Scripture first.</strong> Each day can begin in the <a href="/rooms/scripture">Scripture room</a>, before the feed.</li>
                <li><strong>Accountability that is personal.</strong> The <a href="/quiz.php">digital habits quiz</a>, check-ins, progress and a private journal help you see your own patterns.</li>
                <li><strong>Practices you can repeat.</strong> <a href="/rooms/prayer">Prayer</a> and <a href="/rooms/formation">formation</a> give you something to do instead of reaching for the phone.</li>
                <li><strong>People alongside you.</strong> <a href="/rooms/community">Small groups</a>, <a href="/rooms/service">service</a> and <a href="/rooms/family">family life</a> keep the work from being solitary.</li>
            </ul>

            <h2>What FaithGuard is not</h2>
            <p>FaithGuard is not medical treatment, therapy or a substitute for professional care. If your screen use is seriously affecting your health, safety or relationships, please speak to a doctor or counsellor as well.</p>

            <h2>Your privacy</h2>
            <p>Journal entries, check-ins and quiz results are designed to be visible only to you. The <a href="/privacy.php">privacy policy</a> and <a href="/cookies.php">cookie policy</a> explain the details.</p>

            <p>Questions? <a href="/contact.php">Contact FaithGuard</a>, or browse the <a href="/resources.php">resources</a>.</p>
        </div>
    </article>
</main>
<?php
    require __DIR__ . '/partials/footer.php';
    require __DIR__ . '/partials/scripts.php';
?>
</body>
</html>