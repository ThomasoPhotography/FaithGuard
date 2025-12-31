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
| Fetch latest comparison summaries
|--------------------------------------------------------------------------
*/
$comparisons = FaithGuardRepository::getLatestComparisonSummary($userId);

if (empty($comparisons)): ?>
    <p class="text-muted text-center mb-0">
        No pastoral insights available yet.
    </p>
<?php else: ?>
    <div class="c-pastoral-summary">
        <?php foreach ($comparisons as $row): ?>
            <div class="mb-4">
                <h6 class="mb-1">
                    <?php echo ucfirst($row['addiction_type']); ?>
                </h6>

                <p class="small mb-1 text-<?php echo $row['tone']; ?>">
                    <?php echo htmlspecialchars($row['message']); ?>
                </p>

                <span class="badge bg-light text-dark text-uppercase small">
                    <?php echo $row['trend']; ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
