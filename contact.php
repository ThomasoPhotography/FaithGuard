<?php
    declare (strict_types = 1);

    require_once __DIR__ . '/includes/page_context.php';

    $meta  = fg_page_meta('contact');
    $email = fg_contact_email();
    if ($email === '') {
    error_log('FaithGuard: FAITHGUARD_CONTACT_EMAIL is not set or is invalid.');
    }
    fg_render_head($meta);
    require __DIR__ . '/partials/nav.php';
?>
<main id="main" class="c-main container my-5">
    <article>
        <h1 class="c-main__title">Contact FaithGuard</h1>
        <p class="c-main__subtitle">Questions about a room, your account or our policies? Send us an email.</p>
        <div class="c-prose">
<?php if ($email !== ''): ?>
            <p><a href="mailto:<?php echo fg_e($email) ?>"><?php echo fg_e($email) ?></a></p>
<?php endif; ?>
            <p>If you want to use your rights over your personal data, say so in your message and see the <a href="/privacy.php">privacy policy</a> for what you can ask for.</p>
            <p>For urgent medical or mental health needs, contact your doctor or local emergency services. FaithGuard cannot provide emergency help.</p>
        </div>
    </article>
</main>
<?php
    require __DIR__ . '/partials/footer.php';
    require __DIR__ . '/partials/scripts.php';
?>
</body>
</html>