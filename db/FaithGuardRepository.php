<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../api/helper/CsrfTokenGenerator.php';
class FaithGuardRepository
{
// ==================== USER OPERATIONS ====================
// Find user by ID
    public static function getUserById(int $id): ?array
    {
        return Database::getSingleRow(
            "SELECT id, email, first_name, last_name, avatar_url, bio, bible_language, bible_version, is_admin, is_member, is_active, created_at, last_login
        FROM users
        WHERE id = ?
        LIMIT 1
        ",
            [$id]
        );
    }

// Find user by email (authentication)
    public static function getUserByEmail(string $email): ?array
    {
        return Database::getSingleRow(
            "
        SELECT *
        FROM users
        WHERE email = ?
        LIMIT 1
        ",
            [$email]
        );
    }

// Find user by first and last name
    public static function getUserByName(string $firstName, string $lastName): ?array
    {
        return Database::getSingleRow(
            "SELECT id, email, first_name, last_name, avatar_url, bio, is_admin, is_member, is_active, created_at, last_login
        FROM users
        WHERE first_name = ?
        AND last_name = ?
        LIMIT 1
        ",
            [
                $firstName,
                $lastName,
            ]
        );
    }

// Create new user
    public static function createUser(array $data)
    {
        try {

            $pdo = Database::getConnection();

            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "
            INSERT INTO users
            (
                email,
                password_hash,
                first_name,
                last_name
            )
            VALUES (?, ?, ?, ?)
            "
            );

            $stmt->execute([
                $data['email'],
                $data['password_hash'],
                $data['first_name'] ?? null,
                $data['last_name'] ?? null,
            ]);

            $userId = (int) $pdo->lastInsertId();

            $preferences = self::createUserPreferences($userId);

            $progress = self::createUserProgress($userId);

            if (! $preferences || ! $progress) {

                $pdo->rollBack();

                return false;
            }

            $pdo->commit();

            return $userId;

        } catch (\Throwable $e) {

            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log(
                "createUser failed: " . $e->getMessage()
            );

            return false;
        }
    }

// Update user
    public static function updateUser(int $id, array $data): bool
    {
        $allowed = [
            'email',
            'first_name',
            'last_name',
            'avatar_url',
            'bio',
            'bible_language',
            'bible_version',
        ];

        $sets   = [];
        $values = [];

        foreach ($data as $key => $value) {

            if (in_array($key, $allowed, true)) {

                $sets[]   = "$key = ?";
                $values[] = $value;

            }
        }

        if (empty($sets)) {
            return false;
        }

        $values[] = $id;

        return (bool) Database::execute(
            "
        UPDATE users
        SET " . implode(', ', $sets) . "
        WHERE id = ?
        ",
            $values
        );
    }

// Update last login timestamp
    public static function updateLastLogin(int $userId): bool
    {
        return (bool) Database::execute(
            "
        UPDATE users
        SET last_login = UTC_TIMESTAMP()
        WHERE id = ?
        ",
            [$userId]
        );
    }

// Update password
    public static function updatePassword(
        int $userId,
        string $passwordHash
    ): bool {
        return (bool) Database::execute(
            "
        UPDATE users
        SET password_hash = ?
        WHERE id = ?
        ",
            [
                $passwordHash,
                $userId,
            ]
        );
    }

// ==================== USER PREFERENCES ====================
// Create default user preferences
    public static function createUserPreferences(int $userId): bool
    {
        return (bool) Database::execute(
            "
        INSERT IGNORE INTO user_preferences
        (
            user_id,
            bible_language,
            bible_version,
            bible_book,
            theme,
            notifications_enabled
        )
        VALUES
        (?, 'en', 'NRSVUE', 'Matthew', 'light', 1)
        ",
            [$userId]
        );
    }

// Get user preferences
    public static function getUserPreferences(int $userId): ?array
    {
        return Database::getSingleRow(
            "
        SELECT *
        FROM user_preferences
        WHERE user_id = ?
        LIMIT 1
        ",
            [$userId]
        );
    }

// Update user preferences
    public static function updateUserPreferences(
        int $userId,
        array $data
    ): bool {

        $allowed = [
            'bible_language',
            'bible_version',
            'bible_book',
            'theme',
            'notifications_enabled',
        ];

        $sets   = [];
        $values = [];

        foreach ($data as $key => $value) {

            if (in_array($key, $allowed, true)) {

                $sets[]   = "$key = ?";
                $values[] = $value;

            }

        }

        if (empty($sets)) {
            return false;
        }

        $values[] = $userId;

        return (bool) Database::execute(
            "
        UPDATE user_preferences
        SET " . implode(', ', $sets) . "
        WHERE user_id = ?
        ",
            $values
        );
    }

// ==================== USER PROGRESS ====================
// Create user progress record
    public static function createUserProgress(int $userId): bool
    {
        return (bool) Database::execute(
            "
        INSERT IGNORE INTO user_progress
        (
            user_id,
            streak_days,
            longest_streak,
            total_checkins
        )
        VALUES
        (?, 0, 0, 0)
        ",
            [$userId]
        );
    }

// Get user progress
    public static function getUserProgress(int $userId): ?array
    {
        return Database::getSingleRow(
            "
        SELECT *
        FROM user_progress
        WHERE user_id = ?
        LIMIT 1
        ",
            [$userId]
        );
    }

// Update user progress
    public static function updateUserProgress(
        int $userId,
        array $data
    ): bool {

        $allowed = [
            'streak_days',
            'longest_streak',
            'total_checkins',
            'last_checkin',
            'sobriety_date',
        ];

        $sets   = [];
        $values = [];

        foreach ($data as $key => $value) {

            if (in_array($key, $allowed, true)) {

                $sets[]   = "$key = ?";
                $values[] = $value;

            }

        }

        if (empty($sets)) {
            return false;
        }

        $values[] = $userId;

        return (bool) Database::execute(
            "
        UPDATE user_progress
        SET " . implode(', ', $sets) . "
        WHERE user_id = ?
        ",
            $values
        );
    }

// ==================== CHECK-INS ====================
// Create daily check-in
    public static function createCheckin(array $data): bool
    {
        try {

            $result = Database::execute(
                "
            INSERT INTO checkins
            (
                user_id,
                checkin_date,
                mood_rating,
                notes,
                triggers,
                victories
            )
            VALUES (?, ?, ?, ?, ?, ?)
            ",
                [
                    $data['user_id'],
                    $data['checkin_date'] ?? gmdate('Y-m-d'),
                    $data['mood_rating'] ?? null,
                    $data['notes'] ?? null,
                    $data['triggers'] ?? null,
                    $data['victories'] ?? null,
                ]
            );

            if ($result) {

                Database::execute(
                    "
                UPDATE user_progress
                SET
                    total_checkins = total_checkins + 1,
                    last_checkin = ?
                WHERE user_id = ?
                ",
                    [
                        $data['checkin_date'] ?? gmdate('Y-m-d'),
                        $data['user_id'],
                    ]
                );

            }

            return (bool) $result;

        } catch (\Throwable $e) {

            error_log(
                "createCheckin failed: " . $e->getMessage()
            );

            return false;

        }
    }

// Get user's check-ins
    public static function getUserCheckins(
        int $userId,
        int $limit = 30
    ): array {
        return Database::getRows(
            "
        SELECT *
        FROM checkins
        WHERE user_id = ?
        ORDER BY checkin_date DESC
        LIMIT ?
        ",
            [
                $userId,
                $limit,
            ]
        );
    }

// Get check-in for specific date
    public static function getCheckinByDate(
        int $userId,
        string $date
    ): ?array {
        return Database::getSingleRow(
            "
        SELECT *
        FROM checkins
        WHERE user_id = ?
        AND checkin_date = ?
        LIMIT 1
        ",
            [
                $userId,
                $date,
            ]
        );
    }

// ==================== RESOURCES ====================
// Get resources with optional filters
    public static function getResources(
        ?string $type = null,
        ?string $category = null,
        int $limit = 6
    ): array {
        $sql        = "SELECT * FROM resources";
        $params     = [];
        $conditions = [];
        if ($type !== null) {
            $conditions[] = "type = ?";
            $params[]     = $type;
        }
        if ($category !== null) {
            $conditions[] = "category = ?";
            $params[]     = $category;
        }

        if (! empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $limit = max(1, min($limit, 100));

        $sql .= "ORDER BY is_featured DESC, created_at DESCLIMIT $limit ";

        return Database::getRows(
            $sql,
            $params
        );
    }

// Get resource by ID
    public static function getResourceById(int $id): ?array
    {
        return Database::getSingleRow(
            "
        SELECT *
        FROM resources
        WHERE id = ?
        LIMIT 1
        ",
            [$id]
        );
    }

// Increment resource views
    public static function incrementResourceViews(
        int $id): bool {
        return (bool) Database::execute(
            "
        UPDATE resources
        SET view_count = view_count + 1
        WHERE id = ?
        ",
            [$id]
        );
    }

// Create resource
    public static function createResource(
        array $data): bool {

        return (bool) Database::execute(
            "
        INSERT INTO resources
        (
            title,
            description,
            content,
            type,
            category,
            author,
            url,
            thumbnail_url,
            is_featured
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ",
            [
                $data['title'],
                $data['description'] ?? null,
                $data['content'] ?? null,
                $data['type'],
                $data['category'] ?? null,
                $data['author'] ?? null,
                $data['url'] ?? null,
                $data['thumbnail_url'] ?? null,
                $data['is_featured'] ?? 0,
            ]
        );
    }

// Update resource
    public static function updateResource(
        int $id,
        array $data): bool {

        $allowed = [
            'title',
            'description',
            'content',
            'type',
            'category',
            'author',
            'url',
            'thumbnail_url',
            'is_featured',
        ];

        $sets   = [];
        $values = [];

        foreach ($data as $key => $value) {

            if (in_array($key, $allowed, true)) {

                $sets[]   = "$key = ?";
                $values[] = $value;

            }

        }

        if (empty($sets)) {
            return false;
        }

        $values[] = $id;

        return (bool) Database::execute(
            "
        UPDATE resources
        SET " . implode(', ', $sets) . "
        WHERE id = ?
        ",
            $values
        );
    }

// Delete resource
    public static function deleteResource(
        int $id): bool {
        return (bool) Database::execute(
            "
        DELETE FROM resources
        WHERE id = ?
        ",
            [$id]
        );
    }

// Resource filters
    public static function getResourceFilters(): array
    {

        $types = Database::getRows(
            "
        SELECT DISTINCT type
        FROM resources
        WHERE type IS NOT NULL
        "
        );

        $categories = Database::getRows(
            "
        SELECT DISTINCT category
        FROM resources
        WHERE category IS NOT NULL
        "
        );

        return [
            'types'      =>
            array_column($types, 'type'),

            'categories' =>
            array_column($categories, 'category'),
        ];
    }

// Featured resources
    public static function getFeaturedResources(
        int $limit = 6
    ): array {

        $limit = max(1, min($limit, 100));

        return Database::getRows(
            "
        SELECT *
        FROM resources
        WHERE is_featured = 1
        ORDER BY created_at DESC
        LIMIT $limit
        "
        );
    }

// Search resources
    public static function searchResources(
        string $keyword,
        int $limit = 50
    ): array {

        $limit = max(1, min($limit, 100));

        $keyword = '%' . $keyword . '%';

        return Database::getRows(
            "
        SELECT *
        FROM resources
        WHERE
            title LIKE ?
            OR description LIKE ?
            OR content LIKE ?
        ORDER BY
            is_featured DESC,
            created_at DESC
        LIMIT $limit
        ",
            [
                $keyword,
                $keyword,
                $keyword,
            ]
        );
    }

// Related resources
    public static function getRelatedResources(
        int $resourceId,
        int $limit = 5
    ): array {

        $resource = self::getResourceById($resourceId);

        if (! $resource) {
            return [];
        }

        $limit = max(1, min($limit, 50));

        return Database::getRows(
            "
        SELECT *
        FROM resources
        WHERE id != ?
        AND
        (
            type = ?
            OR category = ?
        )
        ORDER BY
            is_featured DESC,
            created_at DESC
        LIMIT $limit
        ",
            [
                $resourceId,
                $resource['type'],
                $resource['category'],
            ]
        );
    }

// Popular resources
    public static function getPopularResources(
        int $limit = 5
    ): array {

        $limit = max(1, min($limit, 50));

        return Database::getRows(
            "
        SELECT *
        FROM resources
        ORDER BY view_count DESC
        LIMIT $limit
        "
        );
    }

// Recent resources
    public static function getRecentResources(
        int $limit = 5
    ): array {

        $limit = max(1, min($limit, 50));

        return Database::getRows(
            "
        SELECT *
        FROM resources
        ORDER BY created_at DESC
        LIMIT $limit
        "
        );
    }

// New resources
    public static function getNewResources(
        int $limit = 5
    ): array {

        $limit = max(1, min($limit, 50));

        return Database::getRows(
            "
        SELECT *
        FROM resources
        WHERE created_at >= DATE_SUB(
            UTC_TIMESTAMP(),
            INTERVAL 7 DAY
        )
        ORDER BY created_at DESC
        LIMIT $limit
        "
        );
    }

// Resource count
    public static function getResourceCount(
        ?string $type = null,
        ?string $category = null
    ): int {
        $sql = "SELECT COUNT(*) AS count FROM resources";

        $params     = [];
        $conditions = [];

        if ($type !== null) {
            $conditions[] = "type=?";
            $params[]     = $type;
        }

        if ($category !== null) {
            $conditions[] = "category=?";
            $params[]     = $category;
        }

        if (! empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $row = Database::getSingleRow(
            $sql,
            $params
        );

        return (int) ($row['count'] ?? 0);
    }

// Resource statistics
    public static function getResourceStatistics(): array
    {

        $views = Database::getSingleRow(
            "
        SELECT
            SUM(view_count) AS total_views,
            AVG(view_count) AS average_views
        FROM resources
        "
        );

        return [
            'total_views'   => (int) ($views['total_views'] ?? 0),
            'average_views' => (float) ($views['average_views'] ?? 0),
        ];
    }

// Recently viewed resources
    public static function getRecentlyViewedResources(
        int $userId,
        int $limit = 5
    ): array {
        // resource_views table does not exist yet
        return [];
    }

// ==================== POSTS ====================
// Get community posts
    public static function getPosts(
        int $limit = 20,
        int $offset = 0): array {

        $limit  = max(1, min($limit, 100));
        $offset = max(0, $offset);

        return Database::getRows(
            "
        SELECT
            p.*,

            CASE
                WHEN p.is_anonymous = 1
                THEN 'Anonymous'
                ELSE CONCAT(
                    u.first_name,
                    ' ',
                    u.last_name
                )
            END AS username,

            CASE
                WHEN p.is_anonymous = 1
                THEN NULL
                ELSE u.avatar_url
            END AS avatar_url

        FROM posts p

        JOIN users u
            ON p.user_id = u.id

        ORDER BY
            p.is_pinned DESC,
            p.created_at DESC

        LIMIT $limit OFFSET $offset
        "
        );
    }

// Get single post
    public static function getPostById(
        int $postId): ?array {

        return Database::getSingleRow(
            "
        SELECT
            p.*,

            CASE
                WHEN p.is_anonymous = 1
                THEN 'Anonymous'
                ELSE CONCAT(
                    u.first_name,
                    ' ',
                    u.last_name
                )
            END AS username

        FROM posts p

        JOIN users u
            ON p.user_id = u.id

        WHERE p.id = ?

        LIMIT 1
        ",
            [
                $postId,
            ]
        );
    }

// Create post
    public static function createPost(
        array $data): bool {

        return (bool) Database::execute(
            "
        INSERT INTO posts
        (
            user_id,
            title,
            content,
            is_anonymous
        )

        VALUES (?, ?, ?, ?)
        ",
            [
                $data['user_id'],
                $data['title'] ?? null,
                $data['content'],
                $data['is_anonymous'] ?? 0,
            ]
        );
    }

// Update post
    public static function updatePost(
        int $postId,
        int $userId,
        array $data): bool {

        return (bool) Database::execute(
            "
        UPDATE posts
        SET
            title = ?,
            content = ?
        WHERE id = ?
        AND user_id = ?
        ",
            [
                $data['title'] ?? null,
                $data['content'],
                $postId,
                $userId,
            ]
        );
    }

// Delete post
    public static function deletePost(
        int $postId,
        int $userId,
        bool $isAdmin = false): bool {

        if ($isAdmin) {

            return (bool) Database::execute(
                "
            DELETE FROM posts
            WHERE id = ?
            ",
                [
                    $postId,
                ]
            );

        }

        return (bool) Database::execute(
            "
        DELETE FROM posts
        WHERE id = ?
        AND user_id = ?
        ",
            [
                $postId,
                $userId,
            ]
        );
    }

// ==================== COMMENTS ====================

// Get comments for post
    public static function getComments(
        int $postId): array {

        return Database::getRows(
            "
        SELECT

            c.*,

            CASE
                WHEN c.is_anonymous = 1
                THEN 'Anonymous'
                ELSE CONCAT(
                    u.first_name,
                    ' ',
                    u.last_name
                )
            END AS username,


            CASE
                WHEN c.is_anonymous = 1
                THEN NULL
                ELSE u.avatar_url
            END AS avatar_url


        FROM comments c

        JOIN users u
            ON c.user_id = u.id


        WHERE c.post_id = ?

        ORDER BY
            c.created_at ASC
        ",
            [
                $postId,
            ]
        );
    }

// Create comment
    public static function createComment(
        array $data): bool {

        return (bool) Database::execute(
            "
        INSERT INTO comments
        (
            post_id,
            user_id,
            content,
            is_anonymous
        )

        VALUES (?, ?, ?, ?)
        ",
            [
                $data['post_id'],
                $data['user_id'],
                $data['content'],
                $data['is_anonymous'] ?? 0,
            ]
        );
    }

// Update comment
    public static function updateComment(
        int $commentId,
        int $userId,
        string $content): bool {

        return (bool) Database::execute(
            "
        UPDATE comments
        SET content = ?
        WHERE id = ?
        AND user_id = ?
        ",
            [
                $content,
                $commentId,
                $userId,
            ]
        );
    }

// Delete comment
    public static function deleteComment(
        int $commentId,
        int $userId,
        bool $isAdmin): bool {

        if ($isAdmin) {

            return (bool) Database::execute(
                "
            DELETE FROM comments
            WHERE id = ?
            ",
                [
                    $commentId,
                ]
            );

        }

        return (bool) Database::execute(
            "
        DELETE FROM comments
        WHERE id = ?
        AND user_id = ?
        ",
            [
                $commentId,
                $userId,
            ]
        );
    }

// Comment count
    public static function getCommentCount(int $postId): int
    {

        $row = Database::getSingleRow(
            "
        SELECT COUNT(*) AS count
        FROM comments
        WHERE post_id = ?
        ",
            [
                $postId,
            ]
        );

        return (int) ($row['count'] ?? 0);
    }

// User comments
    public static function getUserComments(
        int $userId,
        int $limit = 20,
        int $offset = 0): array {

        $limit  = max(1, min($limit, 100));
        $offset = max(0, $offset);

        return Database::getRows(
            "
        SELECT
            c.*,
            p.title AS post_title

        FROM comments c

        JOIN posts p
            ON c.post_id = p.id

        WHERE c.user_id = ?

        ORDER BY c.created_at DESC

        LIMIT $limit OFFSET $offset
        ",
            [
                $userId,
            ]
        );
    }

// Recent comments for admin
    public static function getRecentComments(int $limit = 20): array
    {
        $limit = max(1, min($limit, 100));
        return Database::getRows(
            "
        SELECT
            c.*,
            CONCAT(
                u.first_name,
                ' ',
                u.last_name
            ) AS username,

            p.title AS post_title


        FROM comments c

        JOIN users u
            ON c.user_id = u.id

        JOIN posts p
            ON c.post_id = p.id


        ORDER BY c.created_at DESC

        LIMIT $limit
        "
        );
    }

// Active commenters
    public static function getMostActiveCommenters(int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));
        return Database::getRows("SELECT u.id, CONCAT(u.first_name,' ', u.last_name) AS username, COUNT(c.id) AS comment_count FROM users u JOIN comments c ON u.id = c.user_id GROUP BY u.id ORDER BY comment_count DESC LIMIT $limit"
        );
    }

// ==================== TESTIMONIALS ====================
// Get approved testimonials
    public static function getTestimonials(
        int $limit = 10): array {

        $limit = max(1, min($limit, 50));

        return Database::getRows(
            "
        SELECT *
        FROM testimonials
        WHERE is_approved = 1
        ORDER BY created_at DESC
        LIMIT $limit
        "
        );
    }

// ==================== BIBLE ====================
// Get Bible books
    public static function getAllBooks(
        string $language = 'en'): array {

        $books = Database::getRows(
            "
        SELECT *
        FROM bible_books
        WHERE language = ?
        ORDER BY id ASC
        ",
            [
                $language,
            ]
        );

        if (! empty($books)) {
            return $books;
        }

        $bibleId = $language === 'nl'
            ? BIBLE_VERSION_NL
            : BIBLE_VERSION_EN;

        $url = BIBLE_API_BASE_URL .
            "/bibles/{$bibleId}/books";

        $response = self::callBibleApi($url);

        if (! $response || ! isset($response['data'])) {
            return [];
        }

        foreach ($response['data'] as $book) {

            Database::execute(
                "
            INSERT IGNORE INTO bible_books
            (
                book_id,
                name,
                abbreviation,
                testament,
                language
            )

            VALUES (?, ?, ?, ?, ?)
            ",
                [
                    $book['id'],
                    $book['name'],
                    $book['abbreviation'] ?? '',
                    $book['testament'],
                    $language,
                ]
            );
        }

        return Database::getRows(
            "
        SELECT *
        FROM bible_books
        WHERE language = ?
        ORDER BY id ASC
        ",
            [
                $language,
            ]
        );
    }

// Get chapters for a Bible book
    public static function getBookChapters(
        string $bookId,
        string $language = 'en'): array {

        $chapters = Database::getRows(
            "
        SELECT *
        FROM bible_chapters
        WHERE book_id = ?
        AND language = ?
        ORDER BY chapter_number ASC
        ",
            [
                $bookId,
                $language,
            ]
        );

        if (! empty($chapters)) {
            return $chapters;
        }

        $bibleId = $language === 'nl'
            ? BIBLE_VERSION_NL
            : BIBLE_VERSION_EN;

        $url = BIBLE_API_BASE_URL .
            "/bibles/{$bibleId}/books/{$bookId}/chapters";

        $response = self::callBibleApi($url);

        if (! $response || ! isset($response['data'])) {
            return [];
        }

        foreach ($response['data'] as $chapter) {

            preg_match(
                '/\.(\d+)$/',
                $chapter['reference'],
                $matches
            );

            $chapterNumber =
            $matches[1] ?? 1;

            Database::execute(
                "
            INSERT IGNORE INTO bible_chapters
            (
                chapter_id,
                book_id,
                chapter_number,
                language
            )

            VALUES (?, ?, ?, ?)
            ",
                [
                    $chapter['id'],
                    $bookId,
                    $chapterNumber,
                    $language,
                ]
            );
        }

        return Database::getRows(
            "
        SELECT *
        FROM bible_chapters
        WHERE book_id = ?
        AND language = ?
        ORDER BY chapter_number ASC
        ",
            [
                $bookId,
                $language,
            ]
        );
    }

// Get verse from cache
    public static function getVerseFromCache(
        string $verseId,
        string $language = 'en'): ?array {

        return Database::getSingleRow(
            "
        SELECT *
        FROM bible_verses
        WHERE verse_id = ?
        AND language = ?
        LIMIT 1
        ",
            [
                $verseId,
                $language,
            ]
        );
    }

// Save verse cache
    public static function saveVerseToCache(
        string $verseId,
        string $chapterId,
        int $verseNumber,
        string $content,
        string $language = 'en'): bool {

        return (bool) Database::execute(
            "
        INSERT INTO bible_verses
        (
            verse_id,
            chapter_id,
            verse_number,
            content,
            language
        )

        VALUES (?, ?, ?, ?, ?)

        ON DUPLICATE KEY UPDATE

            content = VALUES(content)

        ",
            [
                $verseId,
                $chapterId,
                $verseNumber,
                $content,
                $language,
            ]
        );
    }

// Delete cached verse
    public static function deleteVerseFromCache(
        string $verseId,
        string $language = 'en'): bool {

        return (bool) Database::execute(
            "
        DELETE FROM bible_verses

        WHERE verse_id = ?
        AND language = ?

        ",
            [
                $verseId,
                $language,
            ]
        );
    }

// Get verse from Bible API
    public static function getVerseByChapter(
        string $chapterId,
        int $verseNumber,
        string $language = 'en'): ?array {

        $bibleId = $language === 'nl'
            ? BIBLE_VERSION_NL
            : BIBLE_VERSION_EN;

        $url = BIBLE_API_BASE_URL .
            "/bibles/{$bibleId}/chapters/{$chapterId}/verses";

        $response = self::callBibleApi($url);

        if (! $response || ! isset($response['data'])) {
            return null;
        }

        foreach ($response['data'] as $verse) {

            if (
                isset($verse['verse'])
                &&
                (int) $verse['verse'] === $verseNumber
            ) {
                return $verse;
            }

            if (
                isset($verse['reference'])
                &&
                preg_match(
                    '/:(\d+)$/',
                    $verse['reference'],
                    $matches
                )
                &&
                (int) $matches[1] === $verseNumber
            ) {
                return $verse;
            }
        }

        return null;
    }

// Bible API helper
    private static function callBibleApi(
        string $url): ?array {

        $context = stream_context_create(
            [
                'http' => [
                    'method'  => 'GET',

                    'header'  =>
                    "api-key: " . BIBLE_API_KEY . "\r\n" .
                    "Accept: application/json",

                    'timeout' => 10,
                ],
            ]
        );

        $response = @file_get_contents(
            $url,
            false,
            $context
        );

        if (! $response) {
            return null;
        }

        $data = json_decode(
            $response,
            true
        );

        return is_array($data)
            ? $data
            : null;
    }

// ==================== POLICY ====================
// Get policy
    public static function getPolicyContent(
        string $slug): ?array {

        return Database::getSingleRow(
            "
        SELECT *
        FROM policy
        WHERE slug = ?
        LIMIT 1
        ",
            [
                $slug,
            ]
        );
    }

// Create policy
    public static function createPolicy(
        string $slug,
        string $title,
        string $content): bool {

        return (bool) Database::execute(
            "
        INSERT INTO policy
        (
            slug,
            content_title,
            content_text
        )

        VALUES (?, ?, ?)
        ",
            [
                $slug,
                $title,
                $content,
            ]
        );
    }

// Update policy
    public static function updatePolicyContentBySlug(
        string $slug,
        array $data): bool {

        $allowed = [
            'content_title',
            'content_text',
        ];

        $sets   = [];
        $params = [];

        foreach ($data as $key => $value) {

            if (in_array($key, $allowed, true)) {

                $sets[] =
                    "$key = ?";

                $params[] =
                    $value;
            }
        }

        if (empty($sets)) {
            return false;
        }

        $params[] = $slug;

        return (bool) Database::execute(
            "
        UPDATE policy

        SET "
            . implode(', ', $sets)
            . "

        WHERE slug = ?
        ",
            $params
        );
    }

// Delete policy
    public static function deletePolicyBySlug(
        string $slug): bool {

        return (bool) Database::execute(
            "
        DELETE FROM policy
        WHERE slug = ?
        ",
            [
                $slug,
            ]
        );
    }

// ==================== PRAYER REQUESTS ====================
// Get prayer requests
    public static function getPrayerRequests(
        int $limit = 20,
        int $offset = 0): array {

        $limit  = max(1, min($limit, 100));
        $offset = max(0, $offset);

        return Database::getRows(
            "
        SELECT
            p.*,

            CASE
                WHEN p.is_anonymous = 1
                THEN 'Anonymous'
                ELSE CONCAT(
                    u.first_name,
                    ' ',
                    u.last_name
                )
            END AS username

        FROM prayer_requests p

        JOIN users u
            ON p.user_id = u.id

        ORDER BY
            p.created_at DESC

        LIMIT $limit OFFSET $offset
        "
        );
    }

// Create prayer request
    public static function createPrayerRequest(array $data): bool
    {
        return (bool) Database::execute(
            "
        INSERT INTO prayer_requests
        (
            user_id,
            title,
            content,
            is_anonymous
        )

        VALUES (?, ?, ?, ?)
        ",
            [
                $data['user_id'],
                $data['title'] ?? null,
                $data['content'],
                $data['is_anonymous'] ?? 0,
            ]
        );
    }

// Mark prayer as answered
    public static function answerPrayerRequest(int $id): bool
    {
        return (bool) Database::execute(
            "
        UPDATE prayer_requests
        SET is_answered = 1
        WHERE id = ?
        ",
            [$id]
        );
    }

// Increment prayer count
    public static function prayForRequest(int $id): bool
    {
        return (bool) Database::execute(
            "
        UPDATE prayer_requests
        SET prayer_count = prayer_count + 1
        WHERE id = ?
        ",
            [$id]
        );
    }

// ==================== QUIZ ====================
// Get active quiz questions
    public static function getQuizQuestions(): array
    {
        return Database::getRows(
            "
        SELECT *
        FROM quiz_questions
        WHERE is_active = 1
        ORDER BY order_num ASC
        "
        );
    }

// Get quiz answers
    public static function getQuizAnswers(int $questionId): array
    {
        return Database::getRows(
            "
        SELECT *
        FROM quiz_answers
        WHERE question_id = ?
        ORDER BY order_num ASC
        ",
            [$questionId]
        );
    }

// Backwards compatible alias
    public static function getAllQuizQuestions(): array
    {
        return self::getQuizQuestions();
    }

// Get answers for a question
    public static function getAllQuizAnswerOptions(
        int $questionId): array {

        return Database::getRows(
            "
        SELECT *
        FROM quiz_answers
        WHERE question_id = ?
        ORDER BY order_num ASC
        ",
            [
                $questionId,
            ]
        );
    }

// Get latest quiz result
    public static function getLastQuizResult(
        int $userId): ?array {

        return Database::getSingleRow(
            "
        SELECT *
        FROM quiz_results
        WHERE user_id = ?
        ORDER BY taken_at DESC
        LIMIT 1
        ",
            [
                $userId,
            ]
        );
    }

// Save quiz result
    public static function saveQuizResult(array $data): bool
    {
        return (bool) Database::execute(
            "
        INSERT INTO quiz_results
        (
            user_id,
            total_score,
            category_scores,
            recommendations
        )

        VALUES (?, ?, ?, ?)
        ",
            [
                $data['user_id'],
                $data['total_score'],
                json_encode(
                    $data['category_scores'] ?? []
                ),
                $data['recommendations'] ?? null
            ]
        );
    }

// Get user quiz results
    public static function getUserQuizResults(int $userId): array
    {

        return Database::getRows(
            "
        SELECT *
        FROM quiz_results
        WHERE user_id = ?
        ORDER BY taken_at DESC
        ",
            [$userId]
        );
    }

// ==================== BIBLE ====================
// Get Bible books
    public static function getBibleBooks(string $language = 'en'): array
    {

        return Database::getRows(
            "
        SELECT *
        FROM bible_books
        WHERE language = ?
        ORDER BY id ASC
        ",
            [$language]
        );
    }

// Get chapters
    public static function getBibleChapters(
        string $bookId,
        string $language = 'en'): array {

        return Database::getRows(
            "
        SELECT *
        FROM bible_chapters
        WHERE book_id = ?
        AND language = ?
        ORDER BY chapter_number ASC
        ",
            [
                $bookId,
                $language,
            ]
        );
    }

// Get verses
    public static function getBibleVerses(
        string $chapterId,
        string $language = 'en'): array {

        return Database::getRows(
            "
        SELECT *
        FROM bible_verses
        WHERE chapter_id = ?
        AND language = ?
        ORDER BY verse_number ASC
        ",
            [
                $chapterId,
                $language,
            ]
        );
    }

// ==================== SCRIPTURE CACHE ====================

    public static function getCachedScripture(
        string $reference,
        string $version): ?array {

        return Database::getSingleRow(
            "
        SELECT *
        FROM scripture_cache
        WHERE reference = ?
        AND version = ?
        LIMIT 1
        ",
            [
                $reference,
                $version,
            ]
        );
    }

    public static function saveScriptureCache(
        string $reference,
        string $version,
        string $content): bool {

        return (bool) Database::execute(
            "
        INSERT INTO scripture_cache
        (
            reference,
            version,
            content
        )

        VALUES (?, ?, ?)

        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            fetched_at = CURRENT_TIMESTAMP
        ",
            [
                $reference,
                $version,
                $content,
            ]
        );
    }

// ==================== POLICIES ====================

    public static function getPolicy(
        string $slug): ?array {

        return Database::getSingleRow(
            "
        SELECT *
        FROM policy
        WHERE slug = ?
        LIMIT 1
        ",
            [$slug]
        );
    }

    public static function getPolicies(): array
    {
        return Database::getRows(
            "
        SELECT *
        FROM policy
        ORDER BY id ASC
        "
        );
    }

    public static function updatePolicy(
        int $id,
        string $title,
        string $content): bool {

        return (bool) Database::execute(
            "
        UPDATE policy
        SET
            content_title = ?,
            content_text = ?

        WHERE id = ?
        ",
            [
                $title,
                $content,
                $id,
            ]
        );
    }

// ==================== ROLES ====================

    public static function getRoles(): array
    {
        return Database::getRows(
            "
        SELECT *
        FROM roles
        ORDER BY id ASC
        "
        );
    }

// ==================== SESSIONS ====================

    public static function createSession(
        string $token,
        int $userId,
        string $expiresAt): bool {

        return (bool) Database::execute(
            "
        INSERT INTO sessions
        (
            id,
            user_id,
            expires_at
        )

        VALUES (?, ?, ?)
        ",
            [
                $token,
                $userId,
                $expiresAt,
            ]
        );
    }

    public static function getSession(
        string $token): ?array {

        return Database::getSingleRow(
            "
        SELECT *
        FROM sessions
        WHERE id = ?
        AND expires_at > UTC_TIMESTAMP()
        LIMIT 1
        ",
            [$token]
        );
    }

    public static function deleteSession(
        string $token): bool {

        return (bool) Database::execute(
            "
        DELETE FROM sessions
        WHERE id = ?
        ",
            [$token]
        );
    }

// ==================== CSRF ====================
// Create CSRF token
    public static function createCsrfToken(
        ?int $userId = null,
        int $lifetime = 3600): ?string {

        $token = CsrfTokenGenerator::generate();

        $expires = gmdate(
            'Y-m-d H:i:s',
            time() + $lifetime
        );

        $result = Database::execute(
            "
        INSERT INTO csrf_tokens
        (
            user_id,
            token,
            expires_at
        )
        VALUES (?, ?, ?)
        ",
            [
                $userId,
                $token,
                $expires,
            ]
        );

        if (! $result) {
            return null;
        }

        return $token;
    }
// Consume CSRF token
    public static function consumeCsrfToken(
        string $token): bool {

        return (bool) Database::execute(
            "
        DELETE FROM csrf_tokens
        WHERE token = ?
        ",
            [
                $token,
            ]
        );
    }
// Validate CSRF token
    public static function validateCsrfToken(
        string $token): bool {

        if (! CsrfTokenGenerator::isValidFormat($token)) {
            return false;
        }

        $result = Database::getSingleRow(
            "SELECT id
        FROM csrf_tokens
        WHERE token = ?
        AND expires_at > UTC_TIMESTAMP()
        LIMIT 1
        ",
            [$token]
        );

        return ! empty($result);
    }
// Delete CSRF token
    public static function deleteCsrfToken(
        string $token): bool {

        return (bool) Database::execute(
            "
        DELETE FROM csrf_tokens
        WHERE token = ?
        ",
            [$token]
        );
    }
}
