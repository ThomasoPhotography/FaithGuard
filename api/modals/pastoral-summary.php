<?php
    declare (strict_types = 1);
    /* =========================================================
        CORE REQUIREMENTS
    ========================================================= */
    require_once __DIR__ . '/../db/database.php';
    require_once __DIR__ . '/../db/FaithGuardRepository.php';
    require_once __DIR__ . '/../services/QuizComparisonService.php';
    /* =========================================================
        SESSION & AUTH
    ========================================================= */
    session_start();
    if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
        echo '<p class="text-muted text-center mb-0">Please sign in to view insights.</p>';
        exit;
    }
    $userId = (int) $_SESSION['user_id'];
    /* =========================================================
        DATA FETCH
    ========================================================= */
    $comparisons = FaithGuardRepository::getLatestComparisonSummary($userId);
if (empty($comparisons)): ?>
        <p class="text-muted text-center mb-0">
            No pastoral insights available yet.
        </p>
        <?php else: $summary = QuizComparisonService::buildUserSummary($comparisons); ?>
	        <div class="c-pastoral">
	            <?php foreach ($summary as $row): ?>
	                <div class="mb-4">
		                    <h6 class="c-pastoral__addiction mb-1"><?php echo htmlspecialchars($row['addiction']); ?></h6>
		                    <p class="c-pastoral__summary small mb-1 text-<?php echo $row['tone']; ?>"><?php echo htmlspecialchars($row['message']); ?></p>
		                    <span class="c-pastoral__badge badge bg-light text-dark text-uppercase small"><?php echo htmlspecialchars($row['trend']); ?></span>
		                </div>
		            <?php endforeach; ?>
        </div>
    <?php endif; ?>
