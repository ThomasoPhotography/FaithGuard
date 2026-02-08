<!-- Footer -->
<footer class="c-footer">
    <div class="c-footer__inner">
        <div class="c-footer__top">

            <!-- Brand -->
            <div class="c-footer__brand">
                <a href="/" class="c-footer__brand-link">
                    <svg class="c-footer__brand-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 4L6 12V22C6 33.05 13.9 43.22 24 46C34.1 43.22 42 33.05 42 22V12L24 4Z" class="c-footer__brand-shield"/>
                        <path d="M24 16V32M16 24H32" class="c-footer__brand-cross"/>
                        <path d="M24 8C24 8 20 14 20 18C20 20.5 21.5 22.5 24 24C26.5 22.5 28 20.5 28 18C28 14 24 8 24 8Z" class="c-footer__brand-flame"/>
                    </svg>
                    <span class="c-footer__brand-text-logo">FaithGuard</span>
                </a>

                <p class="c-footer__brand-description">
                    Overcoming addiction through Christ. Protecting your digital faith with hope and redemption.
                </p>
            </div>

            <!-- Links -->
            <div class="c-footer__links">

                <div class="c-footer__column">
                    <h4 class="c-footer__column-title">Navigation</h4>
                    <div class="c-footer__column-list">
                        <a href="/" class="c-footer__link">Home</a>
                        <a href="/resources.php" class="c-footer__link">Resources</a>
                        <a href="/about.php" class="c-footer__link">About</a>
                        <a href="/contact.php" class="c-footer__link">Contact</a>
                    </div>
                </div>

                <div class="c-footer__column">
                    <h4 class="c-footer__column-title">Resources</h4>
                    <div class="c-footer__column-list">
                        <a href="/resources.php?type=article" class="c-footer__link">Articles</a>
                        <a href="/resources.php?type=video" class="c-footer__link">Videos</a>
                        <a href="/resources.php?type=prayer" class="c-footer__link">Prayers</a>
                        <a href="/quiz.php" class="c-footer__link">Self Assessment</a>
                    </div>
                </div>

                <div class="c-footer__column">
                    <h4 class="c-footer__column-title">Support</h4>
                    <div class="c-footer__column-list">
                        <a href="/community.php" class="c-footer__link">Community</a>
                        <a href="/prayer.php" class="c-footer__link">Prayer Requests</a>
                        <a href="/contact.php" class="c-footer__link">Get Help</a>
                    </div>
                </div>

            </div>
        </div>

        <div class="c-footer__bottom">
            <p class="c-footer__copyright">
                &copy; <?php echo date('Y'); ?> FaithGuard. All rights reserved.
            </p>

            <div class="c-footer__legal">
                <a href="/terms.php" class="c-footer__legal-link">Terms of Service</a>
                <a href="/privacy.php" class="c-footer__legal-link">Privacy Policy</a>
                <a href="/cookies.php" class="c-footer__legal-link">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bible Verse Modal -->
<div class="modal modal--verse" id="verse-modal">
    <div class="modal__content modal__content--verse">
        <div class="modal__header">
            <h3 class="modal__title">Scripture</h3>
            <button class="modal__close" id="close-verse-modal">
                ✕
            </button>
        </div>

        <div class="modal__body">
            <div class="verse-modal__selector">
                <button class="verse-modal__lang-btn verse-modal__lang-btn--active" data-lang="en">
                    English (NRSVUE)
                </button>
                <button class="verse-modal__lang-btn" data-lang="nl">
                    Nederlands (NBV21)
                </button>
            </div>

            <div class="verse-modal__content" id="verse-content">
                <div class="verse-modal__loading">
                    <span class="spinner"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/footer.js"></script>
</body>
</html>