<?php
    declare (strict_types = 1);
    /** Default policy text, shown until an administrator saves real text in the policy editor. */
?>
        <h2>Who is responsible</h2>
<?php if ($operator !== ''): ?>
        <p>The controller of your personal data is <?php echo fg_e($operator) ?>. You can reach us through the <a href="/contact.php">contact page</a>.</p>
<?php else: ?>
        <p>The operator of FaithGuard is the controller of your personal data. You can reach us through the <a href="/contact.php">contact page</a>.</p>
<?php endif; ?>

        <h2>What we collect</h2>
        <ul>
            <li><strong>Account details:</strong> your name or display name, email address and a hashed password. We never store your password in readable form.</li>
            <li><strong>Your activity:</strong> check-ins, progress and streaks, quiz results and journal entries, only if you use those features.</li>
            <li><strong>Technical data:</strong> a session identifier so that you stay signed in, and basic server logs needed to keep the site secure.</li>
        </ul>

        <h2>Sensitive information</h2>
        <p>What you write in a journal, a prayer request or a check-in can reveal your religious beliefs and information about your wellbeing. The law treats this as special category data. We only process it with your explicit consent, which you give when you create an account and choose to use these features. You can withdraw consent at any time by deleting the entries or your account.</p>

        <h2>Why we use it, and on what basis</h2>
        <ul>
            <li>To provide the features you ask for, such as progress tracking and your journal (performance of our agreement with you).</li>
            <li>To process sensitive information you choose to enter (your explicit consent).</li>
            <li>To keep the service secure and prevent abuse (our legitimate interest).</li>
            <li>To meet legal obligations.</li>
        </ul>

        <h2>Who can see your data</h2>
        <p>Journal entries, check-ins and quiz results are visible only to you. Access for administrators is limited to what is needed to run and secure the service. We do not sell personal data, and we do not use advertising or analytics trackers.</p>
        <p>Scripture text is retrieved by our server from API.Bible. Your account details are not sent to API.Bible. We also use a hosting provider to run the site, which processes data on our behalf.</p>
        <p>Your browser loads Bootstrap, Bootstrap Icons and fonts from third-party content delivery networks (jsDelivr and Google Fonts). Those providers receive your IP address and browser details when a page loads.</p>

        <h2>How long we keep it</h2>
        <p>We keep your data while your account is active. When you delete your account, we delete your personal data, except what the law requires us to keep.</p>

        <h2>Your rights</h2>
        <p>Under the GDPR you can ask to access, correct, delete or restrict your data, receive a copy of it, object to certain uses, and withdraw consent. <a href="/contact.php">Contact us</a> to use any of these rights. You can also complain to the Belgian Data Protection Authority (Gegevensbeschermingsautoriteit) or to the authority in your own country.</p>

        <h2>Security</h2>
        <p>We protect your data with hashed passwords, encrypted connections and server-side access checks. No system is perfectly secure, so please choose a strong, unique password.</p>

        <h2>Cookies</h2>
        <p>See the <a href="/cookies.php">cookie policy</a> for what we store in your browser. Our <a href="/terms.php">terms and conditions</a> describe the rest of the agreement between you and FaithGuard.</p>

        <h2>Changes</h2>
        <p>If we change this policy in a way that matters to you, we will update the date above and tell account holders.</p>