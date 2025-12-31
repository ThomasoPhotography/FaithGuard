<?php
    declare (strict_types = 1);

    /* =========================================================
        CORE REQUIREMENTS
    ========================================================= */
    require_once __DIR__ . "/../db/database.php";
    require_once __DIR__ . "/../db/FaithGuardRepository.php";
    require_once __DIR__ . "/../api/helper/debug.php";
    require_once __DIR__ . "/../api/services/quizTimelineService.php";

    /* =========================================================
        SESSION SETUP
    ========================================================= */
    session_set_cookie_params([
        'lifetime' => 302400,
        'path'     => '/',
        'domain'   => $_SERVER['SERVER_NAME'] ?? '',
        'secure'   => true,
        'httponly' => true,
    ]);
    session_start();

    /* =========================================================
        AUTH GUARD (USER ONLY)
    ========================================================= */
    $is_logged_in = isset($_SESSION['logged_in'], $_SESSION['user_id'])
        && $_SESSION['logged_in'] === true;

    if (! $is_logged_in) {
        header("Location: ../api/auth/login.php");
        exit;
    }

    $userId    = $_SESSION['user_id'] ?? null;
    $user      = FaithGuardRepository::getUserById($userId);
    $timelines = QuizTimelineService::buildUserTimelines($userId);

    if (! is_array($user)) {
        session_destroy();
        header("Location: ../api/auth/login.php");
        exit;
    }

/*
|--------------------------------------------------------------------------
| Fetch timeline data from repository
|--------------------------------------------------------------------------
*/
$timelines = FaithGuardRepository::getUserQuizTimeline($userId);

if (empty($timelines)): ?>
    <p class="text-muted text-center mb-0">
        No assessment history available yet.
    </p>
<?php else: ?>
    <ul class="c-timeline list-unstyled mb-0">
        <?php foreach ($timelines as $entry): ?>
            <li class="c-timeline__item mb-4">
                <div class="c-timeline__marker"></div>

                <div class="c-timeline__content">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong>
                            <?php echo date('F j, Y', strtotime($entry['date'])); ?>
                        </strong>

                        <span class="badge bg-secondary text-uppercase small">
                            <?php echo htmlspecialchars($entry['severity']); ?>
                        </span>
                    </div>

                    <div class="small text-muted">
                        <?php foreach ($entry['addictions'] as $type => $score): ?>
                            <span class="me-2">
                                <?php echo ucfirst($type); ?> (<?php echo $score; ?>)
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
