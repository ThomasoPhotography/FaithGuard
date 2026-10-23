<?php
    declare (strict_types = 1);
?>
<footer class="c-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-12">
                <img class="c-footer__logo" src="/assets/uploads/FaithGuard_Primary_Logo.svg" alt="" width="48" height="48">
                <p class="c-footer__text">&copy; <?php echo fg_e(date('Y')) ?> FaithGuard. All rights reserved. Overcoming addiction through Christ and protecting your digital faith with hope and redemption.</p>
            </div>
            <nav class="col-md-6 col-12 text-md-end mb-0 mt-lg-5" aria-label="Footer">
                <ul class="footer-nav c-footer__nav">
                    <li class="c-footer__item"><a class="c-footer__links" href="/">Home</a></li>
                    <li class="c-footer__item"><a class="c-footer__links" href="/resources.php">Resources</a></li>
                    <li class="c-footer__item"><a class="c-footer__links" href="/quiz.php">Quiz</a></li>
                    <li class="c-footer__item"><a class="c-footer__links" href="/about.php">About</a></li>
                    <li class="c-footer__item"><a class="c-footer__links" href="/contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
        <div class="row c-footer__row">
            <nav class="col-md-6 col-12" aria-label="The eight rooms">
                <ul class="footer-nav c-footer__nav c-footer__nav--rooms">
<?php foreach (rooms() as $room): ?>
                    <li class="c-footer__item"><a class="c-footer__links" href="/rooms/<?php echo fg_e($room['slug']) ?>"><?php echo fg_e($room['name']) ?></a></li>
<?php endforeach; ?>
                </ul>
            </nav>
            <nav class="col-md-6 col-12 text-md-end" aria-label="Legal">
                <ul class="footer-nav c-footer__nav">
                    <li class="c-footer__item"><a class="c-footer__links" href="/terms.php">Terms and conditions</a></li>
                    <li class="c-footer__item"><a class="c-footer__links" href="/privacy.php">Privacy policy</a></li>
                    <li class="c-footer__item"><a class="c-footer__links" href="/cookies.php">Cookie policy</a></li>
                </ul>
            </nav>
        </div>
    </div>
</footer>
<div class="verse-modal" id="verse-modal" role="dialog" aria-modal="true" aria-labelledby="verse-modal-title">
    <div class="verse-modal__panel">
        <div class="verse-modal__header">
            <h2 class="verse-modal__title" id="verse-modal-title">Scripture</h2>
            <button type="button" class="verse-modal__close" id="close-verse-modal" aria-label="Close">&times;</button>
        </div>
        <div class="verse-modal__langs" role="group" aria-label="Bible language">
            <button type="button" class="verse-modal__lang-btn verse-modal__lang-btn--active" data-lang="en">English</button>
            <button type="button" class="verse-modal__lang-btn" data-lang="nl" lang="nl">Nederlands</button>
        </div>
        <div class="verse-modal__body" id="verse-content" aria-live="polite"></div>
    </div>
</div>