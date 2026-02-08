<?php
$pageTitle = 'Dashboard';

require_once __DIR__ . '/db/FaithGuardRepository.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$user = FaithGuardRepository::getUserById($userId);
$progress = FaithGuardRepository::getUserProgress($userId);
$checkins = FaithGuardRepository::getUserCheckins($userId, 7);
$lastQuizResult = FaithGuardRepository::getLastQuizResult($userId);

if (!$user) {
    session_destroy();
    header('Location: /login.php');
    exit;
}

$isAdmin = !empty($user['is_admin']);

require_once __DIR__ . '/partials/header.php';
?>

<div class="dashboard">
    <div class="dashboard__container container">

        <!-- Header -->
        <header class="dashboard__header">
            <h1 class="dashboard__title">
                Welcome, <?php echo htmlspecialchars($user['first_name'] ?: 'Friend'); ?>
            </h1>
            <p class="dashboard__subtitle">
                Every day is a new opportunity to walk forward.
            </p>
        </header>

        <!-- Progress Section -->
        <section class="dashboard__section dashboard__section--progress">
            <h2 class="dashboard__section-title">Progress</h2>

            <div class="dashboard__stats">
                <div class="dashboard__stat-card">
                    <span class="dashboard__stat-value"><?php echo $progress['streak_days'] ?? 0; ?></span>
                    <span class="dashboard__stat-label">Current Streak</span>
                </div>

                <div class="dashboard__stat-card">
                    <span class="dashboard__stat-value"><?php echo $progress['longest_streak'] ?? 0; ?></span>
                    <span class="dashboard__stat-label">Longest Streak</span>
                </div>

                <div class="dashboard__stat-card">
                    <span class="dashboard__stat-value"><?php echo $progress['total_checkins'] ?? 0; ?></span>
                    <span class="dashboard__stat-label">Total Check-ins</span>
                </div>

                <div class="dashboard__stat-card">
                    <span class="dashboard__stat-value"><?php echo count($checkins); ?></span>
                    <span class="dashboard__stat-label">This Week</span>
                </div>
            </div>
        </section>


        <!-- Main Grid -->
        <div class="dashboard__grid">

            <!-- Journal -->
            <section class="dashboard__section dashboard__section--journal">
                <div class="dashboard-card dashboard-card--journal">
                    <h3 class="dashboard-card__title">Journal</h3>
                    <p class="dashboard-card__text">
                        Reflect on your journey. Record struggles, victories, and prayers.
                    </p>
                    <a href="/journal.php" class="btn btn--primary dashboard-card__button">
                        Open Journal
                    </a>
                </div>
            </section>


            <!-- Quiz Assessment -->
            <section class="dashboard__section dashboard__section--quiz">
                <div class="dashboard-card dashboard-card--quiz">
                    <h3 class="dashboard-card__title">Quiz Assessment</h3>

                    <?php if ($lastQuizResult): ?>
                        <div class="dashboard-card__quiz-result">
                            <p class="dashboard-card__text">
                                Last Score: <strong><?php echo $lastQuizResult['total_score']; ?></strong>
                            </p>
                            <p class="dashboard-card__meta">
                                Taken on <?php echo date('M j, Y', strtotime($lastQuizResult['taken_at'])); ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <p class="dashboard-card__text">
                            You have not taken the assessment yet.
                        </p>
                    <?php endif; ?>

                    <a href="/quiz.php" class="btn btn--outline dashboard-card__button">
                        <?php echo $lastQuizResult ? 'Retake Assessment' : 'Take Assessment'; ?>
                    </a>
                </div>
            </section>


            <!-- Scripture Section -->
            <section class="dashboard__section dashboard__section--scripture">
                <div class="dashboard-card dashboard-card--scripture">
                    <h3 class="dashboard-card__title">Scripture</h3>

                    <form class="dashboard-scripture" id="dashboard-scripture-form">
                        <div class="dashboard-scripture__row">
                            <select name="book" class="dashboard-scripture__select">
                                <?php foreach (FaithGuardRepository::getAllBooks() as $book): ?>
                                    <option value="<?php echo htmlspecialchars($book['name']); ?>">
                                        <?php echo htmlspecialchars($book['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select name="chapter" class="dashboard-scripture__select">
                                <?php foreach (FaithGuardRepository::getBookChapters($bookId, $language) as $bookName => $chapters): ?>
                                    <optgroup label="<?php echo htmlspecialchars($bookName); ?>">
                                        <?php foreach ($chapters as $chapterNum): ?>
                                            <option value="<?php echo $chapterNum; ?>">
                                                <?php echo $chapterNum; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>

                            <select name="version" class="dashboard-scripture__select">
                                <option value="NRSVUE">NRSVUE</option>
                                <option value="NBV21">NBV21</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn--ghost dashboard-scripture__button">
                            Load Chapter
                        </button>
                    </form>

                    <div id="dashboard-scripture-content" class="dashboard-scripture__content">
                        Select a book and chapter to begin.
                    </div>
                </div>
            </section>

        </div>


        <!-- Admin Policy Management -->
        <?php if ($isAdmin): ?>
            <section class="dashboard__section dashboard__section--admin">
                <div class="dashboard-card dashboard-card--admin">
                    <h3 class="dashboard-card__title">Policy Management</h3>

                    <div class="dashboard-admin">
                        <a href="/admin/edit-policy.php?type=privacy" class="btn btn--outline dashboard-admin__button">
                            Edit Privacy Policy
                        </a>

                        <a href="/admin/edit-policy.php?type=terms" class="btn btn--outline dashboard-admin__button">
                            Edit Terms of Service
                        </a>

                        <a href="/admin/edit-policy.php?type=community" class="btn btn--outline dashboard-admin__button">
                            Edit Community Guidelines
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    </div>
</div>

<script src="/assets/js/dashboard.js" defer></script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>