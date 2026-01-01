<?php
    declare (strict_types = 1);

    /* =========================================================
   CORE REQUIREMENTS
========================================================= */
    require_once __DIR__ . '/../db/database.php';
    require_once __DIR__ . '/../db/FaithGuardRepository.php';

    /* =========================================================
   SESSION & AUTH
========================================================= */
    session_start();

    if (
        empty($_SESSION['logged_in']) ||
        empty($_SESSION['user_id'])
    ) {
        echo '<p class="text-muted text-center mb-0">Please sign in to view history.</p>';
        exit;
    }

    $userId = (int) $_SESSION['user_id'];

    /* =========================================================
   DATA FETCH
========================================================= */
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
                                <?php echo ucfirst($type); ?> (<?php echo (int) $score; ?>)
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>