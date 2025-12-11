<?php
// /db/FaithGuardRepository.php
// FIX: Use single leading slash for pathing stability.
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
       ANALYTICS
    ============================ */
    public static function getAllAnalytics()
    {
        return Database::getRows("SELECT * FROM analytics ORDER BY created_at DESC");
    }

    public static function getAnalyticsByUserId($userId)
    {
        return Database::getRows("SELECT * FROM analytics WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }

    public static function createAnalytics($eventType, $userId = null, $data = null)
    {
        return Database::execute("INSERT INTO analytics (event_type, user_id, data) VALUES (?, ?, ?)",
            [$eventType, $userId, $data]
        );
    }

    public static function deleteAnalytics($id)
    {
        return Database::execute("DELETE FROM analytics WHERE id = ?", [$id]);
    }

    /* ============================
       DONATIONS
    ============================ */
    public static function getAllDonations()
    {
        return Database::getRows("SELECT * FROM donations ORDER BY created_at DESC");
    }

    public static function getDonationsByUserId($userId)
    {
        return Database::getRows("SELECT * FROM donations WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }

    public static function createDonation($userId, $amount)
    {
        return Database::execute("INSERT INTO donations (user_id, amount) VALUES (?, ?)",
            [$userId, $amount]
        );
    }

    public static function deleteDonation($id)
    {
        return Database::execute("DELETE FROM donations WHERE id = ?", [$id]);
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
        return Database::getRows(
            "SELECT p.*, r.reason, r.user_id AS reporter_id, r.created_at AS reported_at
             FROM posts p
             JOIN reports r ON p.id = r.post_id
             ORDER BY r.created_at DESC"
        );
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

    public static function createResource($title, $content, $tags = null)
    {
        return Database::execute(
            "INSERT INTO resources (title, content, tags) VALUES (?, ?, ?)",
            [$title, $content, $tags]
        );
    }
    public static function updateResource($id, $title, $content, $tags)
    {
        return Database::execute(
            "UPDATE resources SET title = ?, content = ?, tags = ? WHERE id = ?",
            [$title, $content, $tags, $id]
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
    public static function createPolicy($title, $slug, $content, $version, $createdBy)
    {
        return Database::execute(
            "INSERT INTO policies (title, slug, content, version, created_by) VALUES (?, ?, ?, ?, ?)",
            [$title, $slug, $content, $version, $createdBy]
        );
    }
    public static function updatePolicy($id, $title, $slug, $content, $version)
    {
        return Database::execute(
            "UPDATE policies SET title = ?, slug = ?, content = ?, version = ? WHERE id = ?",
            [$title, $slug, $content, $version, $id]
        );
    }
    public static function deletePolicy($id)
    {
        return Database::execute("DELETE FROM policies WHERE id = ?", [$id]);
    }
}
