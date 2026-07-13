<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../api/helper/CsrfTokenGenerator.php';
class FaithGuardRepository
{
    // ==================== USER OPERATIONS ====================
    //Find user by ID
    public static function getUserById(int $id): ?array
    {
        return Database::getSingleRow(
            "SELECT id, email, first_name, last_name, avatar_url, bio, is_admin, is_active, created_at, last_login FROM users WHERE id = ?", [$id]
        );
    }
    //Find user by email (includes password hash for auth)
    public static function getUserByEmail(string $email): ?array
    {
        return Database::getSingleRow(
            "SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]
        );
    }
    //Find user by username
    public static function getUserByName(string $firstName, string $lastName): ?array
    {
        return Database::getSingleRow(
            "SELECT id, email, first_name, last_name, avatar_url, bio, is_admin, is_active, created_at, last_login FROM users WHERE first_name = ? AND last_name = ?", [$firstName, $lastName]
        );
    }
    //Create new user
    public static function createUser(array $data)
    {
        // Insert using Database helper then fetch the created user by email to obtain the ID
        try {
            $rowsAffected = Database::execute(
                "INSERT INTO users (email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?)",
                [
                    $data['email'],
                    $data['password_hash'],
                    $data['first_name'] ?? null,
                    $data['last_name'] ?? null,
                ]
            );

            if (! $rowsAffected) {
                return false;
            }

            // Fetch the user we just created to get the ID
            $user = self::getUserByEmail($data['email']);
            if (! $user || empty($user['id'])) {
                return false;
            }

            $userId = (int) $user['id'];

            // Create default preferences and progress
            self::createUserPreferences($userId);
            self::createUserProgress($userId);

            return $userId;
        } catch (Exception $e) {
            error_log('createUser error: ' . $e->getMessage());
            return false;
        }
    }
    //Update user
    public static function updateUser(int $id, array $data): bool
    {
        $allowed = ['email', 'first_name', 'last_name', 'avatar_url', 'bio'];
        $sets    = [];
        $values  = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $sets[]   = "$key = ?";
                $values[] = $value;
            }
        }
        if (empty($sets)) {
            return false;
        }
        $values[] = $id;
        return Database::execute(
            "UPDATE users SET " . implode(', ', $sets) . " WHERE id = ?", $values
        );
    }
    //Update last login timestamp
    public static function updateLastLogin(int $userId): bool
    {
        return Database::execute(
            "UPDATE users SET last_login = NOW() WHERE id = ?", [$userId]
        );
    }
    //Update password
    public static function updatePassword(int $userId, string $passwordHash): bool
    {
        return Database::execute(
            "UPDATE users SET password_hash = ? WHERE id = ?", [$passwordHash, $userId]
        );
    }

    // ==================== USER PREFERENCES ====================
    //Create default user preferences
    public static function createUserPreferences(int $userId): bool
    {
        return Database::execute(
            "INSERT INTO user_preferences (user_id) VALUES (?)", [$userId]
        );
    }
    //Get user preferences
    public static function getUserPreferences(int $userId): ?array
    {
        return Database::getSingleRow(
            "SELECT * FROM user_preferences WHERE user_id = ?", [$userId]
        );
    }
    //Update user preferences
    public static function updateUserPreferences(int $userId, array $data): bool
    {
        $allowed = ['bible_language', 'bible_version', 'theme', 'notifications_enabled'];
        $sets    = [];
        $values  = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $sets[]   = "$key = ?";
                $values[] = $value;
            }
        }
        if (empty($sets)) {
            return false;
        }
        $values[] = $userId;
        return Database::execute(
            "UPDATE user_preferences SET " . implode(', ', $sets) . " WHERE user_id = ?",
            $values
        );
    }
    // ==================== USER PROGRESS ====================
    //Create user progress record
    public static function createUserProgress(int $userId): bool
    {
        return Database::execute(
            "INSERT INTO user_progress (user_id) VALUES (?)", [$userId]
        );
    }
    //Get user progress
    public static function getUserProgress(int $userId): ?array
    {
        return Database::getSingleRow(
            "SELECT * FROM user_progress WHERE user_id = ?", [$userId]
        );
    }
    //Update user progress
    public static function updateUserProgress(int $userId, array $data): bool
    {
        $allowed = ['streak_days', 'longest_streak', 'total_checkins', 'last_checkin', 'sobriety_date'];
        $sets    = [];
        $values  = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $sets[]   = "$key = ?";
                $values[] = $value;
            }
        }
        if (empty($sets)) {
            return false;
        }
        $values[] = $userId;
        return Database::execute(
            "UPDATE user_progress SET " . implode(', ', $sets) . " WHERE user_id = ?", $values
        );
    }

    // ==================== CHECK-INS ====================
    //Create daily check-in
    public static function createCheckin(array $data)
    {
        return Database::execute(
            "INSERT INTO checkins (user_id, checkin_date, mood_rating, notes, triggers, victories) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['user_id'],
                $data['checkin_date'],
                $data['mood_rating'],
                $data['notes'] ?? null,
                $data['triggers'] ?? null,
                $data['victories'] ?? null,
            ]
        );
    }
    //Get user's check-ins
    public static function getUserCheckins(int $userId, int $limit = 30): array
    {
        return Database::getRows(
            "SELECT * FROM checkins WHERE user_id = ? ORDER BY checkin_date DESC LIMIT ?", [$userId, $limit]
        );
    }
    //Get check-in for specific date
    public static function getCheckinByDate(int $userId, string $date): ?array
    {
        return Database::getSingleRow(
            "SELECT * FROM checkins WHERE user_id = ? AND checkin_date = ?", [$userId, $date]
        );
    }

    // ==================== RESOURCES ====================
    //Get resources with optional filters
    public static function getResources(?string $type = null, ?string $category = null, int $limit = 6): array
    {
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
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql      .= " ORDER BY is_featured DESC, created_at DESC LIMIT ?";
        $params[]  = $limit;

        return Database::getRows($sql, $params);
    }
    //Get resource by ID
    public static function getResourceById(int $id): ?array
    {
        return Database::getSingleRow(
            "SELECT * FROM resources WHERE id = ?", [$id]
        );
    }
    //Increment resource view count
    public static function incrementResourceViews(int $id): bool
    {
        return Database::execute(
            "UPDATE resources SET view_count = view_count + 1 WHERE id = ?", [$id]
        );
    }
    //Create new resource
    public static function createResource(array $data)
    {
        return Database::execute(
            "INSERT INTO resources (title, description, url, type, category, is_featured) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['title'],
                $data['description'] ?? null,
                $data['url'],
                $data['type'] ?? null,
                $data['category'] ?? null,
                $data['is_featured'] ?? false,
            ]
        );
    }
    //Update resource
    public static function updateResource(int $id, array $data): bool
    {
        $allowed = ['title', 'description', 'url', 'type', 'category', 'is_featured'];
        $sets    = [];
        $values  = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $sets[]   = "$key = ?";
                $values[] = $value;
            }
        }
        if (empty($sets)) {
            return false;
        }
        $values[] = $id;
        return Database::execute(
            "UPDATE resources SET " . implode(', ', $sets) . " WHERE id = ?", $values
        );
    }
    //Delete resource
    public static function deleteResource(int $id): bool
    {
        return Database::execute(
            "DELETE FROM resources WHERE id = ?", [$id]
        );
    }
    // Get distinct resource types and categories for filters
    public static function getResourceFilters(): array
    {
        $types      = Database::getRows("SELECT DISTINCT type FROM resources WHERE type IS NOT NULL");
        $categories = Database::getRows("SELECT DISTINCT category FROM resources WHERE category IS NOT NULL");
        return [
            'types'      => array_column($types, 'type'),
            'categories' => array_column($categories, 'category'),
        ];
    }
    // Get featured resources for homepage
    public static function getFeaturedResources(int $limit = 6): array
    {
        return Database::getRows(
            "SELECT * FROM resources WHERE is_featured = 1 ORDER BY created_at DESC LIMIT ?", [$limit]
        );
    }
    // Search resources by keyword in title or description
    public static function searchResources(string $keyword, int $limit = 50): array
    {
        $likeKeyword = '%' . $keyword . '%';
        return Database::getRows(
            "SELECT * FROM resources WHERE (title LIKE ? OR description LIKE ?) ORDER BY is_featured DESC, created_at DESC LIMIT ?",
            [$likeKeyword, $likeKeyword, $limit]
        );
    }
    // Get related resources based on type/category
    public static function getRelatedResources(int $resourceId, int $limit = 5): array
    {
        $resource = self::getResourceById($resourceId);
        if (! $resource) {
            return [];
        }
        $sql    = "SELECT * FROM resources WHERE id != ? AND (type = ? OR category = ?) ORDER BY is_featured DESC, created_at DESC LIMIT ?";
        $params = [$resourceId, $resource['type'], $resource['category'], $limit];
        return Database::getRows($sql, $params);
    }
    // Get most popular resources by view count
    public static function getPopularResources(int $limit = 5): array
    {
        return Database::getRows(
            "SELECT * FROM resources ORDER BY view_count DESC, created_at DESC LIMIT ?", [$limit]
        );
    }
    // Get recently added resources
    public static function getRecentResources(int $limit = 5): array
    {
        return Database::getRows(
            "SELECT * FROM resources ORDER BY created_at DESC LIMIT ?", [$limit]
        );
    }
    // Get resources added in the last 7 days
    public static function getNewResources(int $limit = 5): array
    {
        return Database::getRows(
            "SELECT * FROM resources WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY created_at DESC LIMIT ?", [$limit]
        );
    }
    // Get total count of resources (for pagination)
    public static function getResourceCount(?string $type = null, ?string $category = null): int
    {
        $sql        = "SELECT COUNT(*) as count FROM resources";
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
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $result = Database::getSingleRow($sql, $params);
        return (int) ($result['count'] ?? 0);
    }
    // Get total count of resources matching search keyword
    public static function getSearchResourceCount(string $keyword): int
    {
        $likeKeyword = '%' . $keyword . '%';
        $result      = Database::getSingleRow(
            "SELECT COUNT(*) as count FROM resources WHERE (title LIKE ? OR description LIKE ?)",
            [$likeKeyword, $likeKeyword]
        );
        return (int) ($result['count'] ?? 0);
    }
    // Get total count of related resources
    public static function getRelatedResourceCount(int $resourceId): int
    {
        $resource = self::getResourceById($resourceId);
        if (! $resource) {
            return 0;
        }
        $result = Database::getSingleRow(
            "SELECT COUNT(*) as count FROM resources WHERE id != ? AND (type = ? OR category = ?)",
            [$resourceId, $resource['type'], $resource['category']]
        );
        return (int) ($result['count'] ?? 0);
    }
    // Get Resource statistics (e.g. total views, average views)
    public static function getResourceStatistics(): array
    {
        $totalViews   = Database::getSingleRow("SELECT SUM(view_count) as total_views FROM resources");
        $averageViews = Database::getSingleRow("SELECT AVG(view_count) as average_views FROM resources");
        return [
            'total_views'   => (int) ($totalViews['total_views'] ?? 0),
            'average_views' => (float) ($averageViews['average_views'] ?? 0),
        ];
    }
    // Get resource counts by type and category for dashboard analytics
    public static function getResourceCountsByTypeAndCategory(): array
    {
        $types      = Database::getRows("SELECT type, COUNT(*) as count FROM resources WHERE type IS NOT NULL GROUP BY type");
        $categories = Database::getRows("SELECT category, COUNT(*) as count FROM resources WHERE category IS NOT NULL GROUP BY category");
        return [
            'by_type'     => array_column($types, 'count', 'type'),
            'by_category' => array_column($categories, 'count', 'category'),
        ];
    }
    // Get recently viewed resources for a user (requires user activity tracking)
    public static function getRecentlyViewedResources(int $userId, int $limit = 5): array
    {
        return Database::getRows(
            "SELECT r.* FROM resource_views rv JOIN resources r ON rv.resource_id = r.id WHERE rv.user_id = ? ORDER BY rv.viewed_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }
    // Get Resources by slug (for friendly URLs)
    public static function getResourceBySlug(string $slug): ?array
    {
        return Database::getSingleRow(
            "SELECT * FROM resources WHERE slug = ?", [$slug]
        );
    }
    // ==================== POSTS ====================
    //Get community posts
    public static function getPosts(int $limit = 20, int $offset = 0): array
    {
        return Database::getRows(
            "SELECT p.*, u.username, u.avatar_url FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.is_pinned DESC, p.created_at DESC LIMIT ? OFFSET ?", [$limit, $offset]
        );
    }
    //Create post
    public static function createPost(array $data)
    {
        return Database::execute(
            "INSERT INTO posts (user_id, title, content, is_anonymous) VALUES (?, ?, ?, ?)",
            [
                $data['user_id'],
                $data['title'],
                $data['content'],
                $data['is_anonymous'] ?? false,
            ]
        );
    }

    // ==================== COMMENTS ====================
    // Get comments for a specific post
    public static function getComments(int $postId): array
    {
        return Database::getRows(
            "SELECT c.*, u.username, u.avatar_url FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC",
            [$postId]
        );
    }
    // Create a comment for a specific post
    public static function createComment(array $data)
    {
        return Database::execute(
            "INSERT INTO comments (post_id, user_id, content, is_anonymous) VALUES (?, ?, ?, ?)",
            [
                $data['post_id'],
                $data['user_id'],
                $data['content'],
                $data['is_anonymous'] ?? false,
            ]
        );
    }
    // Delete a comment by ID - only the comment owner or an admin can delete
    public static function deleteComment(int $commentId, int $userId, bool $isAdmin): bool
    {
        if ($isAdmin) {
            // Admin can delete any comment
            return Database::execute(
                "DELETE FROM comments WHERE id = ?", [$commentId]
            );
        } else {
            // Regular user can only delete their own comment
            return Database::execute(
                "DELETE FROM comments WHERE id = ? AND user_id = ?", [$commentId, $userId]
            );
        }
    }
    // Get comment count for a specific post
    public static function getCommentCount(int $postId): int
    {
        $result = Database::getSingleRow(
            "SELECT COUNT(*) as count FROM comments WHERE post_id = ?", [$postId]
        );
        return (int) ($result['count'] ?? 0);
    }
    // Get all comments made by a specific user
    public static function getUserComments(int $userId, int $limit = 20, int $offset = 0): array
    {
        return Database::getRows(
            "SELECT c.*, p.title as post_title FROM comments c JOIN posts p ON c.post_id = p.id WHERE c.user_id = ? ORDER BY c.created_at DESC LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );
    }
    // Get the most recent comments across all posts (for admin dashboard)
    public static function getRecentComments(int $limit = 20): array
    {
        return Database::getRows(
            "SELECT c.*, u.username, p.title as post_title FROM comments c JOIN users u ON c.user_id = u.id JOIN posts p ON c.post_id = p.id ORDER BY c.created_at DESC LIMIT ?",
            [$limit]
        );
    }
    // Get the most active commenters (users with the highest number of comments)
    public static function getMostActiveCommenters(int $limit = 10): array
    {
        return Database::getRows(
            "SELECT u.id, u.username, COUNT(c.id) as comment_count FROM users u JOIN comments c ON u.id = c.user_id GROUP BY u.id ORDER BY comment_count DESC LIMIT ?",
            [$limit]
        );
    }

    // ==================== TESTIMONIALS ====================
    // Get all testimonials
    public static function getTestimonials(int $limit = 10): array
    {
        return Database::getRows(
            "SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT ?", [$limit]
        );
    }

    // ==================== QUIZ ====================
    //Get all quiz questions with answers
    public static function getQuizQuestions(): array
    {
        $questions = Database::getRows("SELECT * FROM quiz_questions WHERE is_active = 1 ORDER BY order_num");
        foreach ($questions as &$question) {
            $question['answers'] = Database::getRows("SELECT * FROM quiz_answers WHERE question_id = ? ORDER BY order_num", [$question['id']]);
        }
        return $questions;
    }
    // Backwards-compatible alias used by some pages
    public static function getAllQuizQuestions(): array
    {
        return self::getQuizQuestions();
    }

    // Get all answer options for a specific quiz question
    public static function getAllQuizAnswerOptions(int $questionId): array
    {
        return Database::getRows("SELECT * FROM quiz_answers WHERE question_id = ? ORDER BY order_num ASC", [$questionId]);
    }
    //Save quiz result
    public static function saveQuizResult(array $data)
    {
        return Database::execute(
            "INSERT INTO quiz_results (user_id, total_score, category_scores, recommendations) VALUES (?, ?, ?, ?)",
            [
                $data['user_id'],
                $data['total_score'],
                json_encode($data['category_scores'] ?? []),
                $data['recommendations'] ?? null
            ]
        );
    }
    // Get user's last quiz result
    public static function getLastQuizResult(int $userId): ?array
    {
        return Database::getSingleRow(
            "SELECT * FROM quiz_results WHERE user_id = ? ORDER BY taken_at DESC LIMIT 5", [$userId]
        );
    }

    // ==================== SESSIONS ====================
    //Create session
    public static function createSession(string $sessionId, int $userId, int $expiresAt): bool
    {
        return Database::execute(
            "INSERT INTO sessions (id, user_id, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))", [$sessionId, $userId, $expiresAt]
        );
    }
    //Get session by ID
    public static function getSession(string $sessionId): ?array
    {
        return Database::getSingleRow(
            "SELECT s.*, u.id AS user_id, u.first_name AS username, u.email, u.is_admin FROM sessions s JOIN users u ON s.user_id = u.id WHERE s.id = ? AND s.expires_at > NOW()", [$sessionId]
        );
    }
    //Delete session
    public static function deleteSession(string $sessionId): bool
    {
        return Database::execute(
            "DELETE FROM sessions WHERE id = ?", [$sessionId]
        );
    }
    //Delete all user sessions
    public static function deleteUserSessions(int $userId): bool
    {
        return Database::execute(
            "DELETE FROM sessions WHERE user_id = ?", [$userId]
        );
    }
    //Clean expired sessions
    public static function cleanExpiredSessions(): bool
    {
        return Database::execute(
            "DELETE FROM sessions WHERE expires_at < NOW()"
        );
    }

    // ==================== BIBLE ====================
    // Get all Bible books (with caching in DB)
    public static function getAllBooks(string $language = 'en'): array
    {
        $books = Database::getRows(
            "SELECT * FROM bible_books WHERE language = ? ORDER BY name ASC", [$language]
        );
        if (! empty($books)) {
            return $books;
        }
                                                                             // Select Bible version based on language
        $bibleId = $language === 'nl' ? BIBLE_VERSION_NL : BIBLE_VERSION_EN; //NBV21 (When request is approved), now NLD1939 for Dutch, NRSVUE for English
        $url     = BIBLE_API_BASE_URL . "/bibles/{$bibleId}/books";
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    "api-key: " . BIBLE_API_KEY,
                    "Accept: application/json",
                ],
            ],
        ]);
        $response = @file_get_contents($url, false, $context);
        if (! $response) {
            return [];
        }
        $data = json_decode($response, true);
        if (! isset($data['data'])) {
            return [];
        }
        foreach ($data['data'] as $book) {
            Database::execute(
                "INSERT IGNORE INTO bible_books (id, name, abbreviation, testament, language) VALUES (?, ?, ?, ?, ?)",
                [
                    $book['id'],
                    $book['name'],
                    $book['abbreviation'] ?? '',
                    $book['testament'],
                    $language,
                ]
            );
        }
        // Return freshly stored data
        return Database::getRows(
            "SELECT * FROM bible_books WHERE language = ? ORDER BY name ASC", [$language]
        );
    }
    // Get chapters for a specific book
    public static function getBookChapters(string $bookId, string $language = 'en'): array
    {
        // Check cache first
        $chapters = Database::getRows(
            "SELECT * FROM bible_chapters WHERE book_id = ? AND language = ? ORDER BY chapter_number ASC", [$bookId, $language]
        );
        if (! empty($chapters)) {
            return $chapters;
        }
                                                                             // Select Bible version based on language
        $bibleId = $language === 'nl' ? BIBLE_VERSION_NL : BIBLE_VERSION_EN; //NBV21 (When request is approved), now NLD1939 for Dutch, NRSVUE for English
        $url     = BIBLE_API_BASE_URL . "/bibles/{$bibleId}/books/{$bookId}/chapters";
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'api-key: ' . BIBLE_API_KEY,
                    'Accept: application/json',
                ],
            ],
        ]);
        $response = @file_get_contents($url, false, $context);
        if (! $response) {
            return [];
        }
        $data = json_decode($response, true);
        if (! isset($data['data'])) {
            return [];
        }
        foreach ($data['data'] as $chapter) {
            // Extract chapter number from reference (e.g. "Gen.1" => 1)
            preg_match('/\.(\d+)$/', $chapter['reference'], $matches);
            $chapterNumber = $matches[1] ?? 1;
            Database::execute(
                "INSERT IGNORE INTO bible_chapters (id, book_id, chapter_number, language) VALUES (?, ?, ?, ?)",
                [
                    $chapter['id'],
                    $bookId,
                    $chapterNumber,
                    $language,
                ]
            );
        }
        // Return freshly stored data
        return Database::getRows(
            "SELECT * FROM bible_chapters WHERE book_id = ? AND language = ? ORDER BY chapter_number ASC", [$bookId, $language]
        );
    }

    // Verse cache: get from DB
    public static function getVerseFromCache(string $bibleId, string $chapterId, int $verseNumber): ?array
    {
        $row = Database::getSingleRow(
            "SELECT * FROM bible_verses WHERE bible_id = ? AND chapter_id = ? AND verse_number = ? LIMIT 1",
            [$bibleId, $chapterId, $verseNumber]
        );
        return $row ?: null;
    }

    // Verse cache: save fetched verse
    public static function saveVerseToCache(string $bibleId, string $chapterId, int $verseNumber, string $content): bool
    {
        return (bool) Database::execute(
            "INSERT INTO bible_verses (bible_id, chapter_id, verse_number, content) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE content = VALUES(content), fetched_at = CURRENT_TIMESTAMP",
            [$bibleId, $chapterId, $verseNumber, $content]
        );
    }
    public static function deleteVerseFromCache(string $bibleId, string $chapterId, int $verseNumber): bool
    {
        return (bool) Database::execute(
            "DELETE FROM bible_verses WHERE bible_id = ? AND chapter_id = ? AND verse_number = ?",
            [$bibleId, $chapterId, $verseNumber]
        );
    }
    public static function getVerseByChapter(string $chapterId, int $verseNumber, string $language = 'en'): ?array
    {
                                                                             // Select Bible version based on language
        $bibleId = $language === 'nl' ? BIBLE_VERSION_NL : BIBLE_VERSION_EN; //NBV21 (When request is approved), now NLD1939 for Dutch, NRSVUE for English
        $url     = BIBLE_API_BASE_URL . "/bibles/{$bibleId}/chapters/{$chapterId}/verses";
        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => [
                    'api-key: ' . BIBLE_API_KEY,
                    'Accept: application/json',
                ],
                'timeout' => 5,
            ],
        ]);
        $resp = @file_get_contents($url, false, $context);
        if (! $resp) {
            return null;
        }
        $data = json_decode($resp, true);
        foreach ($data['data'] ?? [] as $v) {
            if (isset($v['verse']) && (int) $v['verse'] === $verseNumber) {
                return $v;
            }
            if (isset($v['reference']) && preg_match('/:(\d+)$/', $v['reference'], $m) && (int) $m[1] === $verseNumber) {
                return $v;
            }
        }
        return null;
    }

    // ==================== POLICY ====================
    // Get policy by slug
    public static function getPolicyContent(string $slug): ?array
    {
        return Database::getSingleRow(
            "SELECT * FROM policy WHERE slug = ? LIMIT 1", [$slug]
        );
    }

    // Create a new policy (if not exists)
    public static function createPolicy(string $slug, string $title, string $content)
    {
        return Database::execute(
            "INSERT INTO policy (slug, content_title, content_text) VALUES (?, ?, ?)",
            [$slug, $title, $content]
        );
    }

    // Update policy by slug
    public static function updatePolicyContentBySlug(string $slug, array $data): bool
    {
        $sets   = [];
        $params = [];
        if (isset($data['content_title'])) {
            $sets[]   = 'content_title = ?';
            $params[] = $data['content_title'];
        }
        if (isset($data['content_text'])) {
            $sets[]   = 'content_text = ?';
            $params[] = $data['content_text'];
        }
        if (empty($sets)) {
            return false;
        }
        $params[] = $slug;
        $sql      = "UPDATE policy SET " . implode(', ', $sets) . ", updated_at = CURRENT_TIMESTAMP WHERE slug = ?";
        return (bool) Database::execute($sql, $params);
    }

    // Delete policy by slug
    public static function deletePolicyBySlug(string $slug): bool
    {
        return (bool) Database::execute(
            "DELETE FROM policy WHERE slug = ?", [$slug]
        );
    }

    // ==================== CSRF TOKEN MANAGEMENT ====================
    /**
     * Generate and store a new CSRF token for a user or session
     *
     * Tokens are generated with an encoded biblical character for spiritual identity.
     * Format: {timestamp}_{id}_{biblical_char}_{random}
     * Example: 1720864000_12345_f_a8b3c9d2
     *
     * @param int|null $userId User ID, or null for session-based token
     * @param int $expirationSeconds Token expiration time in seconds (default: 3600 = 1 hour)
     * @param string|null $biblicalRef Optional biblical reference to encode
     * @return string The generated CSRF token with encoded biblical character
     */
    public static function createCsrfToken(?int $userId = null, int $expirationSeconds = 3600, ?string $biblicalRef = null): string
    {
        try {
            // Insert placeholder row to get the auto-increment ID
            $stmt = Database::execute(
                "INSERT INTO csrf_tokens (user_id, token, expires_at) VALUES (?, ?, ?)",
                [$userId, 'placeholder', date('Y-m-d H:i:s', time() + $expirationSeconds)]
            );

            // Get the last inserted ID from the database connection
            $conn   = Database::getConnection();
            $lastId = $conn->lastInsertId();
            $conn   = null;

            // Generate token with the database ID and optional biblical reference
            $token     = CsrfTokenGenerator::generateToken((int) $lastId, $biblicalRef);
            $expiresAt = date('Y-m-d H:i:s', time() + $expirationSeconds);

            // Update the placeholder with the actual token
            Database::execute(
                "UPDATE csrf_tokens SET token = ?, expires_at = ? WHERE id = ?",
                [$token, $expiresAt, $lastId]
            );

            return $token;
        } catch (\Throwable $e) {
            error_log('Failed to create CSRF token: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Validate a CSRF token
     *
     * @param string $token The token to validate
     * @param int|null $userId Optional user ID to check token belongs to this user
     * @return bool True if token is valid and not expired
     */
    public static function validateCsrfToken(string $token, ?int $userId = null): bool
    {
        if (empty($token)) {
            return false;
        }

        try {
            $sql    = "SELECT id, user_id, expires_at FROM csrf_tokens WHERE token = ? AND expires_at > NOW()";
            $params = [$token];

            if ($userId !== null) {
                $sql      .= " AND user_id = ?";
                $params[]  = $userId;
            }

            $row = Database::getSingleRow($sql, $params);
            return $row !== null && $row !== false;
        } catch (\Throwable $e) {
            error_log('Failed to validate CSRF token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Consume a CSRF token (delete it after validation to prevent replay attacks)
     *
     * @param string $token The token to consume
     * @return bool True if token was successfully consumed
     */
    public static function consumeCsrfToken(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        try {
            return (bool) Database::execute(
                "DELETE FROM csrf_tokens WHERE token = ?",
                [$token]
            );
        } catch (\Throwable $e) {
            error_log('Failed to consume CSRF token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up expired CSRF tokens (call this periodically)
     *
     * @return int Number of tokens deleted
     */
    public static function cleanupExpiredCsrfTokens(): int
    {
        try {
            return Database::execute(
                "DELETE FROM csrf_tokens WHERE expires_at < NOW()"
            );
        } catch (\Throwable $e) {
            error_log('Failed to cleanup expired CSRF tokens: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all valid CSRF tokens for a user
     *
     * @param int $userId The user ID
     * @return array Array of token records
     */
    public static function getUserCsrfTokens(int $userId): array
    {
        try {
            return Database::getRows(
                "SELECT id, token, expires_at, created_at FROM csrf_tokens WHERE user_id = ? AND expires_at > NOW() ORDER BY created_at DESC",
                [$userId]
            );
        } catch (\Throwable $e) {
            error_log('Failed to get user CSRF tokens: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get the biblical reference encoded in a CSRF token
     *
     * @param string $token The CSRF token
     * @return string|null The biblical reference name (e.g., "Genesis", "John"), or null if invalid
     */
    public static function getCsrfTokenBiblicalRef(string $token): ?string
    {
        return CsrfTokenGenerator::extractBiblicalRef($token);
    }

    /**
     * Get the database ID embedded in a CSRF token
     *
     * @param string $token The CSRF token
     * @return int|null The database ID of the token record
     */
    public static function getCsrfTokenId(string $token): ?int
    {
        return CsrfTokenGenerator::extractId($token);
    }

    /**
     * Get the creation timestamp from a CSRF token
     *
     * @param string $token The CSRF token
     * @return int|null The Unix timestamp when token was created
     */
    public static function getCsrfTokenTimestamp(string $token): ?int
    {
        return CsrfTokenGenerator::extractTimestamp($token);
    }

    /**
     * Check if a CSRF token has valid format
     *
     * @param string $token The CSRF token
     * @return bool True if token structure is valid
     */
    public static function isCsrfTokenFormatValid(string $token): bool
    {
        return CsrfTokenGenerator::isValidFormat($token);
    }
}
