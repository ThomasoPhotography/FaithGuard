<?php
    declare (strict_types = 1);

    /* =========================================================
   SESSION + SECURITY
========================================================= */
    session_set_cookie_params([
    'lifetime' => 302400,
    'path'     => '/',
    'domain'   => $_SERVER['SERVER_NAME'] ?? '',
    'secure'   => true,
    'httponly' => true,
    ]);
    session_start();

    require_once __DIR__ . '/db/database.php';
    require_once __DIR__ . '/db/FaithGuardRepository.php';
    require_once __DIR__ . '/api/helper/debug.php';
    require_once __DIR__ . '/api/helper/user.php';

    /* =========================================================
   AUTH GUARD
========================================================= */
    $is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

    $user        = null;
    $accountName = 'Guest';
    $user_role   = 'user';

    if ($is_logged_in && isset($_SESSION['user_id'])) {
    $user_data = FaithGuardRepository::getUserById($_SESSION['user_id']);
    if ($user_data) {
        $user        = normalize_user($user_data);
        $accountName = $user['display_name'] ?? ($user['email'] ?? 'User');
        $user_role   = $user['role'] ?? 'user';
    } else {
        session_destroy();
        header("Location: /");
        exit;
    }
    }
    /* =========================================================
   USER CONTEXT
========================================================= */
    $userId = $_SESSION['user_id'] ?? null;
    if (! $userId) {
    session_destroy();
    header("Location: /index.php");
    exit;
    }

    // Ensure account name and role come from normalized user
    $accountName = $accountName ?? ($user['display_name'] ?? ($user['email'] ?? 'User'));
    $user_role   = $user_role ?? ($user['role'] ?? 'user');

    /* =========================================================
   QUIZ DATA
========================================================= */
    $questions = FaithGuardRepository::getAllQuizQuestions();

    $addictionQuestion = null;
    foreach ($questions as $q) {
    if ((int) $q['id'] === 1) {
        $addictionQuestion = $q;
        break;
    }
    }

    if (! $addictionQuestion) {
    throw new RuntimeException('Addiction selection question (ID 1) missing.');
    }

    /* =========================================================
   ADDICTION TYPES (still static by design)
========================================================= */
    $addictionTypes = [
    'Pornography'            => 'Pornography',
    'Alcohol'                => 'Alcohol',
    'Substance Use'          => 'Substance',
    'Gambling'               => 'Gambling',
    'Digital / Social Media' => 'Digital',
    'Smoking / Vaping'       => 'Smoking',
    ];
?>
<?php
    require_once __DIR__ . '/includes/seo.php';
    require_once __DIR__ . '/includes/rooms.php';
    require_once __DIR__ . '/includes/pages.php';
    $meta = fg_private_meta('Digital habits quiz', '/quiz.php');
    fg_render_head($meta);
?>
<?php require __DIR__ . '/partials/nav.php'; ?>
    <!-- Main Content -->
    <main id="main" class="c-main container my-5">
    <section class="c-main__section c-quiz">
        <h1 class="c-main__title text-center mb-3">FaithGuard Self-Assessment</h1>
        <p class="c-main__text text-center mb-5">
            Answer honestly. This assessment helps guide you toward Scripture-rooted support.
        </p>
        <div class="c-progress mb-4">
            <div class="c-progress__track">
                <div class="c-progress__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
            </div>
        </div>
        <form class="c-quiz__content c-quiz__form" action="/api/quiz/submit.php" method="POST">

            <!-- =====================================================
                ADDICTION SELECTION (ID 1)
            ===================================================== -->
            <section class="c-quiz__question" data-step="0" data-question-id="<?php echo (int) $addictionQuestion['id']; ?>" data-required="true">
                <h4 class="mb-2">
                    <?php echo htmlspecialchars($addictionQuestion['question'], ENT_QUOTES, 'UTF-8'); ?>
                </h4>
                <?php if (! empty($addictionQuestion['description'])): ?>
                    <p class="text-muted mb-3">
                        <?php echo htmlspecialchars($addictionQuestion['description'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                <?php endif; ?>
                <?php foreach ($addictionTypes as $label => $value): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input js-addiction-checkbox" type="checkbox" name="addiction_types[]" id="addiction_<?php echo htmlspecialchars($value, ENT_QUOTES); ?>" value="<?php echo htmlspecialchars($value, ENT_QUOTES); ?>">
                        <label class="form-check-label" for="addiction_<?php echo htmlspecialchars($value, ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </section>

            <!-- =====================================================
                ALL QUESTIONS EXCEPT ID 1
            ===================================================== -->
            <?php $stepIndex = 1;foreach ($questions as $q): $questionId = (int) $q['id'];if ($questionId === 1) {continue;}
                $answerOptions              = FaithGuardRepository::getAllQuizAnswerOptions($questionId); ?>
                <section class="c-quiz__question" data-step="<?php echo $stepIndex; ?>" data-question-id="<?php echo $questionId; ?>" data-required="true">
                    <h5 class="mb-3">
                        <?php echo htmlspecialchars($q['question'], ENT_QUOTES, 'UTF-8'); ?>
                    </h5>
                    <?php foreach ($answerOptions as $optIndex => $option): ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[<?php echo $questionId; ?>]" value="<?php echo htmlspecialchars($option['id'] ?? $optIndex, ENT_QUOTES); ?>" required>
                            <label class="form-check-label">
                                <?php echo htmlspecialchars($option['label'] ?? ($option['value'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php $stepIndex++;endforeach; ?>
            <!-- =====================================================
                NAVIGATION
            ===================================================== -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" id="prevButton" class="btn c-btn">Back</button>
                <button type="button" id="nextButton" class="btn c-btn">Next</button>
                <button type="submit" id="submitButton" class="btn c-btn" hidden>
                    Submit Assessment
                </button>
            </div>
        </form>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
<?php require __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>