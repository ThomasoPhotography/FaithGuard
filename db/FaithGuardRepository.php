<?php
require_once dirname(__FILE__) . "/database.php";

class FaithGuardRepository
{
    /* ============================
        USERS
    ============================ */
    public static function getUserByEmail($email)
    {
        return Database::getSingleRow("SELECT id, password_hash FROM users WHERE email = ?", [$email]);
    }
    public static function getUserById($id)
    {
        return Database::getSingleRow("SELECT * FROM users WHERE id = ?", [$id]);
    }
    public static function createUser($email, $passwordHash, $name = null, $role = 'user')
    {
        return Database::execute("INSERT INTO users (email, password_hash, name, role) VALUES (?, ?, ?, ?)",
            [$email, $passwordHash, $name, $role]
        );
    }
    public static function updateUser($id, $email, $name, $role)
    {
        return Database::execute("UPDATE users SET email = ?, name = ?, role = ? WHERE id = ?",
            [$email, $name, $role, $id]
        );
    }
    public static function deleteUser($id)
    {
        return Database::execute("DELETE FROM users WHERE id = ?", [$id]);
    }
    /* ============================
        MESSAGES
    ============================ */
    public static function getAllMessages()
    {
        return Database::getRows("SELECT * FROM messages ORDER BY created_at DESC");
    }
    public static function getMessagesByUserId($userId)
    {
        return Database::getRows(
            "SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ? ORDER BY created_at DESC",
            [$userId, $userId]
        );
    }
    public static function getInboxByUserId($userId)
    {
        return Database::getRows("SELECT * FROM messages WHERE receiver_id = ? ORDER BY created_at DESC", [$userId]);
    }
    public static function createMessage($senderId, $receiverId, $content)
    {
        return Database::execute(
            "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)",
            [$senderId, $receiverId, $content]
        );
    }
    public static function deleteMessage($id)
    {
        return Database::execute("DELETE FROM messages WHERE id = ?", [$id]);
    }
    /* ============================
        POSTS
    ============================ */
    public static function getAllPosts()
    {
        return Database::getRows("SELECT * FROM posts ORDER BY created_at DESC");
    }
    public static function getPostsByUserId($userId)
    {
        return Database::getRows("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }
    public static function getAllReportedPosts()
    {
        return Database::getRows("SELECT p.*, r.reason, r.user_id AS reporter_id, r.created_at AS reported_at FROM posts p JOIN reports r ON p.id = r.post_id ORDER BY r.created_at DESC");
    }
    public static function getPostById($id)
    {
        return Database::getSingleRow("SELECT * FROM posts WHERE id = ?", [$id]);
    }
    public static function createPost($userId, $content)
    {
        return Database::execute("INSERT INTO posts (user_id, content) VALUES (?, ?)", [$userId, $content]);
    }
    public static function updatePost($id, $content)
    {
        return Database::execute("UPDATE posts SET content = ? WHERE id = ?", [$content, $id]);
    }
    public static function deletePost($id)
    {
        return Database::execute("DELETE FROM posts WHERE id = ?", [$id]);
    }
    /* ============================
        POST REPLIES
    ============================ */
    public static function getRepliesByPostId($postId)
    {
        return Database::getRows("SELECT * FROM post_replies WHERE post_id = ? ORDER BY created_at ASC", [$postId]);
    }
    public static function createReply($postId, $userId, $content)
    {
        return Database::execute(
            "INSERT INTO post_replies (post_id, user_id, content) VALUES (?, ?, ?)",
            [$postId, $userId, $content]
        );
    }
    public static function deleteReply($id)
    {
        return Database::execute("DELETE FROM post_replies WHERE id = ?", [$id]);
    }
    /* ============================
        PRAYERS
    ============================ */
    public static function getAllPrayers()
    {
        return Database::getRows("SELECT * FROM prayers ORDER BY created_at DESC");
    }
    public static function getPrayersByUserId($userId)
    {
        return Database::getRows("SELECT * FROM prayers WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }
    public static function createPrayer($userId, $content)
    {
        return Database::execute("INSERT INTO prayers (user_id, content) VALUES (?, ?)", [$userId, $content]);
    }
    public static function deletePrayer($id)
    {
        return Database::execute("DELETE FROM prayers WHERE id = ?", [$id]);
    }
    /* ============================
        PROGRESS LOGS
    ============================ */
    public static function getAllProgressLogs()
    {
        return Database::getRows("SELECT * FROM progress_logs ORDER BY checkin_date DESC");
    }
    public static function getProgressLogsByUserId($userId)
    {
        return Database::getRows("SELECT * FROM progress_logs WHERE user_id = ? ORDER BY checkin_date DESC", [$userId]);
    }
    public static function createProgressLog($userId, $milestone = null)
    {
        return Database::execute("INSERT INTO progress_logs (user_id, milestone) VALUES (?, ?)",
            [$userId, $milestone]
        );
    }
    public static function deleteProgressLog($id)
    {
        return Database::execute("DELETE FROM progress_logs WHERE id = ?", [$id]);
    }
    /* ============================
        QUIZ — QUESTIONS
    ============================ */
    public static function getAllQuizQuestions()
    {
        return Database::getRows("SELECT * FROM quiz_questions");
    }
    public static function getQuizQuestionById($id)
    {
        return Database::getSingleRow("SELECT * FROM quiz_questions WHERE id = ?", [$id]);
    }
    public static function createQuizQuestion($question, $options)
    {
        return Database::execute("INSERT INTO quiz_questions (question, options) VALUES (?, ?)",
            [$question, $options]
        );
    }
    public static function updateQuizQuestion($id, $question, $options)
    {
        return Database::execute("UPDATE quiz_questions SET question = ?, options = ? WHERE id = ?",
            [$question, $options, $id]
        );
    }
    public static function deleteQuizQuestion($id)
    {
        return Database::execute("DELETE FROM quiz_questions WHERE id = ?", [$id]);
    }
    /* ============================
        QUIZ — RESULTS
    ============================ */
    public static function getAllQuizResults()
    {
        return Database::getRows("SELECT * FROM quiz_results ORDER BY created_at DESC");
    }
    public static function getQuizResultsByUserId($userId)
    {
        return Database::getRows("SELECT * FROM quiz_results WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }
    public static function createQuizResult($userId, $addictionType, $answersJson, $totalScore)
    {
        return Database::execute(
            "INSERT INTO quiz_results (user_id, addiction_type, answers_json, total_score) VALUES (?, ?, ?, ?)",
            [$userId, $addictionType, $answersJson, $totalScore]
        );
    }
    public static function deleteQuizResult($id)
    {
        return Database::execute("DELETE FROM quiz_results WHERE id = ?", [$id]);
    }
    public static function saveQuizResult($userId, $quizId, $answers, $score)
    {
        return Database::execute("INSERT INTO quiz_results (user_id, quiz_id, answers, score) VALUES (?, ?, ?, ?)", [$userId, $quizId, json_encode($answers), $score]);
    }
    public static function getQuizResults($userId)
    {
        return Database::getRows("SELECT * FROM quiz_results WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }
    /* ============================
        QUIZ — ANSWER OPTIONS
    ============================ */
    public static function getAllQuizAnswerOptions()
    {
        return Database::getRows("SELECT * FROM quiz_answer_options");
    }
    public static function getQuizAnswerOptionsByQuestionId($questionId)
    {
        return Database::getRows("SELECT * FROM quiz_answer_options WHERE question_id = ?", [$questionId]);
    }
    public static function createQuizAnswerOption($questionId, $optionText, $value)
    {
        return Database::execute("INSERT INTO quiz_answer_options (question_id, option_text, value) VALUES (?, ?, ?)",
            [$questionId, $optionText, $value]
        );
    }
    public static function updateQuizAnswerOption($id, $optionText, $value)
    {
        return Database::execute("UPDATE quiz_answer_options SET option_text = ?, value = ? WHERE id = ?",
            [$optionText, $value, $id]
        );
    }
    public static function deleteQuizAnswerOption($id)
    {
        return Database::execute("DELETE FROM quiz_answer_options WHERE id = ?", [$id]);
    }
    /* ============================
        QUIZ — ATTEMPTS
    ============================ */
    public static function createQuizAttempt(int $userId): int
    {
        Database::execute(
            "INSERT INTO quiz_attempts (user_id) VALUES (?)",
            [$userId]
        );

        return Database::getSingleRow("SELECT LAST_INSERT_ID() AS id")['id'];
    }
    public static function getPreviousAttemptId(int $userId, int $currentAttemptId): ?int
    {
        $row = Database::getSingleRow("SELECT id FROM quiz_attempts WHERE user_id = ? AND id < ? ORDER BY id DESC LIMIT 2", [$userId, $currentAttemptId]);
        return $row ? (int) $row['id'] : null;
    }

    /* ============================
        QUIZ — ATTEMPT ADDICTIONS
    ============================ */
    public static function saveAttemptAddiction(int $attemptId, string $addiction, int $score): void
    {
        Database::execute("INSERT INTO quiz_attempt_addictions (attempt_id, addiction_type, score) VALUES (?, ?, ?)", [$attemptId, $addiction, $score]);
    }
    public static function getAttemptAddictions(int $attemptId): array
    {
        $rows   = Database::getRows("SELECT addiction_type, score FROM quiz_attempt_addictions WHERE attempt_id = ?", [$attemptId]);
        $result = [];
        foreach ($rows as $row) {
            $result[$row['addiction_type']] = (int) $row['score'];
        }
        return $result;
    }
    /* ============================
        RESOURCES
    ============================ */
    public static function getAllResources()
    {
        return Database::getRows("SELECT * FROM resources ORDER BY created_at DESC");
    }
    public static function getResourceById($id)
    {
        return Database::getSingleRow("SELECT * FROM resources WHERE id = ?", [$id]);
    }
    public static function getResourceBySlug($slug)
    {
        return Database::getSingleRow("SELECT * FROM resources WHERE slug = ?", [$slug]);
    }
    public static function createResource($title, $slug, $content_text, $content_visual, $createdBy)
    {
        return Database::execute("INSERT INTO resources (title, slug, content_text, content_visual, created_by) VALUES (?, ?, ?, ?, ?)",
            [$title, $slug, $content_text, $content_visual, $createdBy]
        );
    }
    public static function updateResource($id, $title, $slug, $content_text, $content_visual)
    {
        return Database::execute("UPDATE resources SET title = ?, slug = ?, content_text = ?, content_visual = ? WHERE id = ?",
            [$title, $slug, $content_text, $content_visual, $id]
        );
    }
    public static function deleteResource($id)
    {
        return Database::execute("DELETE FROM resources WHERE id = ?", [$id]);
    }
    /* ============================
        RESOURCE TAGS
    ============================ */
    public static function getTagsByResourceId($resourceId)
    {
        return Database::getRows("SELECT * FROM resource_tags WHERE resource_id = ?", [$resourceId]);
    }
    public static function createResourceTag($resourceId, $tag)
    {
        return Database::execute("INSERT INTO resource_tags (resource_id, tag) VALUES (?, ?)",
            [$resourceId, $tag]
        );
    }
    public static function deleteResourceTag($id)
    {
        return Database::execute("DELETE FROM resource_tags WHERE id = ?", [$id]);
    }
    public static function getMatchedResourcesForCategory(string $category, array $tags, int $limit = 5): array
    {
        if (empty($tags)) {
            return [];
        }
        $conditions = [];
        $params     = [];
        foreach ($tags as $tag) {
            $conditions[] = "FIND_IN_SET(?, tags)";
            $params[]     = $tag;
        }
        // Score resources by number of matching tags
        $scoreSql = implode(' + ', array_fill(0, count($tags), 'FIND_IN_SET(?, tags)'));
        $params   = array_merge($params, $tags);
        $params[] = $limit;
        $sql      = " SELECT *, ($scoreSql) AS relevance_score FROM resources WHERE category = ? AND (" . implode(' OR ', $conditions) . ") ORDER BY relevance_score DESC, created_at DESC LIMIT ?";
        array_unshift($params, $category);
        return Database::getRows($sql, $params);
    }
    /* ============================
        ROLES
    ============================ */
    public static function getAllRoles()
    {
        return Database::getRows("SELECT * FROM roles");
    }
    public static function getRoleById($id)
    {
        return Database::getSingleRow("SELECT * FROM roles WHERE id = ?", [$id]);
    }
    public static function createRole($name)
    {
        return Database::execute("INSERT INTO roles (name) VALUES (?)", [$name]);
    }
    public static function deleteRole($id)
    {
        return Database::execute("DELETE FROM roles WHERE id = ?", [$id]);
    }
    /* ============================
        SESSIONS
    ============================ */
    public static function getSessionByToken($token)
    {
        return Database::getSingleRow("SELECT * FROM sessions WHERE token = ?", [$token]);
    }
    public static function createSession($userId, $token, $expiresAt)
    {
        return Database::execute(
            "INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)",
            [$userId, $token, $expiresAt]
        );
    }
    public static function deleteSession($token)
    {
        return Database::execute("DELETE FROM sessions WHERE token = ?", [$token]);
    }
    /* ============================
        POLICIES
    ============================ */
    public static function getAllPolicies()
    {
        return Database::getRows("SELECT * FROM policies ORDER BY updated_at DESC");
    }
    public static function getPolicyBySlug($slug)
    {
        return Database::getSingleRow("SELECT * FROM policies WHERE slug = ?", [$slug]);
    }
    public static function getPolicyById($id)
    {
        return Database::getSingleRow("SELECT * FROM policies WHERE id = ?", [$id]);
    }
    public static function getPolicyContent($slug)
    {
        $result = Database::getSingleRow("SELECT content_title, content_text, created_at, updated_at FROM policies WHERE slug = ? LIMIT 1", [$slug]);
        return $result ? $result : null;
    }
    public static function createPolicy($title, $slug, $contentTitle, $contentText, $version, $createdBy)
    {
        return Database::execute("INSERT INTO policies (title, slug, content_title, content_text, version, created_by) VALUES (?, ?, ?, ?, ?, ?)", [$title, $slug, $contentTitle, $contentText, $version, $createdBy]);
    }
    public static function updatePolicy($id, $title, $slug, $contentTitle, $contentText, $version)
    {
        return Database::execute("UPDATE policies SET title = ?, slug = ?, content_title = ?, content_text = ?, version = ? WHERE id = ?", [$title, $slug, $contentTitle, $contentText, $version, $id]);
    }
    public static function deletePolicy($id)
    {
        return Database::execute("DELETE FROM policies WHERE id = ?", [$id]);
    }
    /* ============================
        JOURNAL ENTRIES
    ============================ */
    public static function getJournalEntriesByUserId($userId)
    {
        return Database::getRows("SELECT * FROM journal_entries WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }
    public static function createJournalEntry($userId, $content_text, $isRelated)
    {
        return Database::execute("INSERT INTO journal_entries (user_id, content_text, is_addiction_related) VALUES (?, ?, ?)", [$userId, $content_text, $isRelated ? 1 : 0]);
    }
    public static function getEncouragement($addictionType)
    {
        $data = [
            'pornography' => [
                'verse' => "Job 31:1",
                'text'  => "I made a covenant with my eyes not to look lustfully at a young woman.",
            ],
            'alcohol'     => [
                'verse' => "1 Corinthians 10:13",
                'text'  => "No temptation has overtaken you except what is common to mankind. And God is faithful...",
            ],
            'general'     => [
                'verse' => "James 4:7",
                'text'  => "Submit yourselves, then, to God. Resist the devil, and he will flee from you.",
            ],
        ];
        return $data[$addictionType] ?? $data['general'];
    }
    /* ============================
        C1 — PASTORAL RESOURCES
    ============================ */
    public static function getResourcesByTags(array $tags): array
    {
        if (empty($tags)) {
            return [];
        }
        $conditions = [];
        $params     = [];
        foreach ($tags as $tag) {
            $conditions[] = 'FIND_IN_SET(?, tags)';
            $params[]     = $tag;
        }
        $sql = "SELECT * FROM resources WHERE " . implode(' OR ', $conditions) . " ORDER BY created_at DESC";
        return Database::getRows($sql, $params);
    }
    public static function getResourcesByAddiction(string $addiction): array
    {
        return Database::getRows(
            "SELECT * FROM resources WHERE FIND_IN_SET(?, tags)",
            [$addiction]
        );
    }
    /* ================================================
        C2-E — PROGRESS COMPARISON & CHANGE DETECTION
    ================================================ */
    public static function getLastQuizAttempt($userId)
    {
        return Database::getSingleRow(
            "SELECT * FROM quiz_results WHERE user_id = ? ORDER BY created_at DESC LIMIT 2",
            [$userId]
        );
    }
    public static function insertAttemptComparison(array $data): void
    {
        Database::execute("INSERT INTO quiz_attempt_comparisons (attempt_id, addiction_type, previous_score, current_score, delta, trend) VALUES (:attempt_id, :addiction_type, :previous_score, :current_score, :delta, :trend)", $data);
    }
    public static function getComparisonsByAttempt(int $attemptId): array
    {
        return Database::getRows("SELECT * FROM quiz_attempt_comparisons WHERE attempt_id = ? ORDER BY addiction_type ASC", [$attemptId]);
    }
    /* ================================================
        C2-F — TIMELINE DATA
    ================================================ */
    public static function getAddictionTimeline(int $userId, string $addictionType): array{
        return Database::getRows("SELECT qa.id AS attempt_id, qa.created_at AS attempt_date, qaa.score AS score FROM quiz_attempts qa JOIN quiz_attempt_addictions qaa ON qa.id = qaa.attempt_id WHERE qa.user_id = ? AND qaa.addiction_type = ? ORDER BY qa.created_at ASC", [$userId, $addictionType]);
    }
    public static function getUserAddictionHistory(int $userId):array{
        return Database::getRows("SELECT DISTINCT addiction_type FROM quiz_attempt_addictions WHERE attempt_id IN (SELECT id FROM quiz_attempts WHERE user_id = ?)", [$userId]);
    }
}
