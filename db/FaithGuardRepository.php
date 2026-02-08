<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/config.php';
class FaithGuardRepository
{
    // ==================== USER OPERATIONS ====================
        //Find user by ID
        public static function getUserById(int $id): ?array {
            return Database::getSingleRow(
                "SELECT id, full_name, email, first_name, last_name, avatar_url, bio, is_admin, is_active, created_at, last_login FROM users WHERE id = ?", [$id]
            );
        }
        //Find user by email (includes password hash for auth)
        public static function getUserByEmail(string $email): ?array{
            return Database::getSingleRow(
                "SELECT * FROM users WHERE email = ? AND is_active = 1",[$email]
            );
        }
        //Find user by username
        public static function getUserByName(string $firstName, string $lastName): ?array{
            return Database::getSingleRow(
                "SELECT id, email, first_name, last_name, avatar_url, bio, is_admin, is_active, created_at, last_login FROM users WHERE first_name = ? AND last_name = ?", [$firstName, $lastName]
            );
        }
        //Create new user
        public static function createUser(array $data): int | false {
            $userId = Database::execute(
                "INSERT INTO users (full_name, email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?, ?)",
                [
                    $data['full_name'] ?? null,
                    $data['email'],
                    $data['password_hash'],
                    $data['first_name'] ?? null,
                    $data['last_name'] ?? null,
                ]
            );
            if ($userId) {
                // Create default preferences
                self::createUserPreferences($userId);
                // Create progress record
                self::createUserProgress($userId);
            }
            return $userId;
        }
        //Update user
        public static function updateUser(int $id, array $data): bool {
            $allowed = ['full_name', 'email', 'first_name', 'last_name', 'avatar_url', 'bio'];
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
        public static function updateLastLogin(int $userId): bool {
            return Database::execute(
                "UPDATE users SET last_login = NOW() WHERE id = ?", [$userId]
            );
        }
        //Update password
        public static function updatePassword(int $userId, string $passwordHash): bool {
            return Database::execute(
                "UPDATE users SET password_hash = ? WHERE id = ?", [$passwordHash, $userId]
            );
        }

    // ==================== USER PREFERENCES ====================
        //Create default user preferences
        public static function createUserPreferences(int $userId): bool {
            return Database::execute(
                "INSERT INTO user_preferences (user_id) VALUES (?)", [$userId]
            );
        }
        //Get user preferences
        public static function getUserPreferences(int $userId): ?array {
            return Database::getSingleRow(
                "SELECT * FROM user_preferences WHERE user_id = ?", [$userId]
            );
        }
        //Update user preferences
        public static function updateUserPreferences(int $userId, array $data): bool {
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
        public static function createUserProgress(int $userId): bool {
            return Database::execute(
                "INSERT INTO user_progress (user_id) VALUES (?)", [$userId]
            );
        }
        //Get user progress
        public static function getUserProgress(int $userId): ?array {
            return Database::getSingleRow(
                "SELECT * FROM user_progress WHERE user_id = ?", [$userId]
            );
        }
        //Update user progress
        public static function updateUserProgress(int $userId, array $data): bool {
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
        public static function createCheckin(array $data): int | false {
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
        public static function getUserCheckins(int $userId, int $limit = 30): array {
            return Database::getRows(
                "SELECT * FROM checkins WHERE user_id = ? ORDER BY checkin_date DESC LIMIT ?", [$userId, $limit]
            );
        }
        //Get check-in for specific date
        public static function getCheckinByDate(int $userId, string $date): ?array {
            return Database::getSingleRow(
                "SELECT * FROM checkins WHERE user_id = ? AND checkin_date = ?", [$userId, $date]
            );
        }

    // ==================== RESOURCES ====================
        //Get all resources
        public static function getResources(string $type, string $category, int $limit = 50): array {
            return Database::getRows(
                "SELECT * FROM resources WHERE type = ? AND category = ? ORDER BY is_featured DESC, created_at DESC LIMIT ?", [$type, $category, $limit]
            );
        }
        //Get resource by ID
        public static function getResourceById(int $id): ?array {
            return Database::getSingleRow(
                "SELECT * FROM resources WHERE id = ?", [$id]
            );
        }
        //Increment resource view count
        public static function incrementResourceViews(int $id): bool {
            return Database::execute(
                "UPDATE resources SET view_count = view_count + 1 WHERE id = ?", [$id]
            );
        }
    // ==================== POSTS ====================
        //Get community posts
        public static function getPosts(int $limit = 20, int $offset = 0): array {
            return Database::getRows(
                "SELECT p.*, u.username, u.avatar_url FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.is_pinned DESC, p.created_at DESC LIMIT ? OFFSET ?", [$limit, $offset]
            );
        }
        //Create post
        public static function createPost(array $data): int | false {
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

    // ==================== QUIZ ====================
        //Get all quiz questions with answers
        public static function getQuizQuestions(): array {
            $questions = Database::getRows("SELECT * FROM quiz_questions WHERE is_active = 1 ORDER BY order_num");
            foreach ($questions as &$question) {
                $question['answers'] = Database::getRows("SELECT * FROM quiz_answers WHERE question_id = ? ORDER BY order_num", [$question['id']]);
            }
            return $questions;
        }
        //Save quiz result
        public static function saveQuizResult(array $data): int | false {
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
        public static function getLastQuizResult(int $userId): ?array {
            return Database::getSingleRow(
                "SELECT * FROM quiz_results WHERE user_id = ? ORDER BY taken_at DESC LIMIT 5", [$userId]
            );
        }

    // ==================== SESSIONS ====================
        //Create session
        public static function createSession(string $sessionId, int $userId, int $expiresAt): bool {
            return Database::execute(
                "INSERT INTO sessions (id, user_id, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))", [$sessionId, $userId, $expiresAt]
            );
        }
        //Get session by ID
        public static function getSession(string $sessionId): ?array {
            return Database::getSingleRow(
                "SELECT s.*, u.id as user_id, u.username, u.email, u.is_admin FROM sessions s JOIN users u ON s.user_id = u.id WHERE s.id = ? AND s.expires_at > NOW()", [$sessionId]
            );
        }
        //Delete session
        public static function deleteSession(string $sessionId): bool {
            return Database::execute(
                "DELETE FROM sessions WHERE id = ?", [$sessionId]
            );
        }
        //Delete all user sessions
        public static function deleteUserSessions(int $userId): bool {
            return Database::execute(
                "DELETE FROM sessions WHERE user_id = ?", [$userId]
            );
        }
        //Clean expired sessions
        public static function cleanExpiredSessions(): bool {
            return Database::execute(
                "DELETE FROM sessions WHERE expires_at < NOW()"
            );
        }

    // ==================== BIBLE ====================
        // Get all Bible books (with caching in DB)
        public static function getAllBooks(string $language = 'en'): array {
            $books = Database::getRows(
                "SELECT * FROM bible_books WHERE language = ? ORDER BY name ASC", [$language]
            );
            if (!empty($books)) {
                return $books;
            }
            // Select Bible version based on language
            $bibleId = $language === 'nl' ? BIBLE_VERSION_NL : BIBLE_VERSION_EN; //NBV21 (When request is approved), now NLD1939 for Dutch, NRSVUE for English
            $url = BIBLE_API_BASE_URL . "/bibles/{$bibleId}/books";
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        "api-key: " . BIBLE_API_KEY,
                        "Accept: application/json"
                        ]
                ]
            ]);
            $response = @file_get_contents($url, false, $context);
            if (!$response){
                return [];
            }
            $data = json_decode($response, true);
            if(!isset($data['data'])){
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
                        $language
                    ]
                );
            }
            // Return freshly stored data
            return Database::getRows(
                "SELECT * FROM bible_books WHERE language = ? ORDER BY name ASC", [$language]
            );
        }
        // Get chapters for a specific book
        public static function getBookChapters(string $bookId, string $language = 'en'): array{
            // Check cache first
            $chapters = Database::getRows(
                "SELECT * FROM bible_chapters WHERE book_id = ? AND language = ? ORDER BY chapter_number ASC", [$bookId, $language]
            );
            if (!empty($chapters)) {
                return $chapters;
            }
            // Select Bible version based on language
            $bibleId = $language === 'nl' ? BIBLE_VERSION_NL : BIBLE_VERSION_EN; //NBV21 (When request is approved), now NLD1939 for Dutch, NRSVUE for English
            $url = BIBLE_API_BASE_URL . "/bibles/{$bibleId}/books/{$bookId}/chapters";
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        'api-key: ' . BIBLE_API_KEY,
                        'Accept: application/json'
                    ]
                ]
            ]);
            $response = @file_get_contents($url, false, $context);
            if (!$response){
                return [];
            }
            $data = json_decode($response, true);
            if(!isset($data['data'])){
                return [];
            }
            foreach ($data['data'] as $chapter){
                // Extract chapter number from reference (e.g. "Gen.1" => 1)
                preg_match('/\.(\d+)$/', $chapter['reference'], $matches);
                $chapterNumber = $matches[1] ?? 1;
                Database::execute(
                    "INSERT IGNORE INTO bible_chapters (id, book_id, chapter_number, language) VALUES (?, ?, ?, ?)",
                    [
                        $chapter['id'],
                        $bookId,
                        $chapterNumber,
                        $language
                    ]
                );
            }
            // Return freshly stored data
            return Database::getRows(
                "SELECT * FROM bible_chapters WHERE book_id = ? AND language = ? ORDER BY chapter_number ASC", [$bookId, $language]
            );
        }
}
