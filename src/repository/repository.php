<?php

require_once dirname(__FILE__) . "/../model/resource.php";
require_once dirname(__FILE__) . "/../model/testimony.php";
require_once dirname(__FILE__) . "/../model/quiz.php";
require_once dirname(__FILE__) . "/../model/user.php";
require_once dirname(__FILE__) . "/../database/database.php";

class FaithGuardRepository
{
    private static function getConnection()
    {
        // Set up for using PDO
        $user = 'root';
        $pass = '';
        $host = 'localhost';
        // Fill in the correct database name (after importing it into your phpMyAdmin)
        $db_name = 'faithguard_db';
        // Set up DSN
        $dsn = "mysql:host=$host;dbname=$db_name";
        $db = new PDO($dsn, $user, $pass);
        return $db;
    }

    public static function getRows($sql, $params = [], $type = null)
    {
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        if ($type == null) {
            $rows = $stmt->fetchAll(); // Returns an array with named arrays
        } else {
            $rows = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $type); // Returns an array with objects of a class
        }
        $conn = null;
        return $rows;
    }

    public static function getSingleRow($sql, $params = [], $type = null)
    {
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        if ($type == null) {
            $row = $stmt->fetch();
        } else {
            $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $type);
            $row = $stmt->fetch(); // Returns 1 object of a class
        }
        $conn = null;
        return $row;
    }

    public static function execute($sql, $params = [])
    {
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $aantalRijen = $stmt->rowCount();
        return $aantalRijen;
    }

    // User-specific methods
    public static function getAllUsers()
    {
        return self::getRows("SELECT * FROM users", [], 'User');
    }

    public static function getUserById($id)
    {
        return self::getSingleRow("SELECT * FROM users WHERE user_id = ?", [$id], 'User');
    }

    public static function getUserByUsername($username)
    {
        return self::getSingleRow("SELECT * FROM users WHERE username = ?", [$username], 'User');
    }

    public static function addUser($username, $passwordHash, $isAdmin = false)
    {
        return self::execute("INSERT INTO users (username, password, is_admin) VALUES (?, ?, ?)", [$username, $passwordHash, $isAdmin]);
    }

    public static function updateUser($id, $username, $passwordHash, $isAdmin)
    {
        return self::execute("UPDATE users SET username = ?, password = ?, is_admin = ? WHERE user_id = ?", [$username, $passwordHash, $isAdmin, $id]);
    }

    public static function deleteUser($id)
    {
        return self::execute("DELETE FROM users WHERE user_id = ?", [$id]);
    }

    // Quiz-specific methods
    public static function getAllQuizzes()
    {
        return self::getRows("SELECT * FROM quizzes", [], 'Quiz');
    }

    public static function getQuizById($id)
    {
        return self::getSingleRow("SELECT * FROM quizzes WHERE quiz_id = ?", [$id], 'Quiz');
    }

    public static function addQuiz($userId, $duration, $accountability, $resourcesOfInterest, $spiritualConnection, $primaryGoal)
    {
        return self::execute("INSERT INTO quizzes (user_id, duration, accountability, resources_of_interest, spiritual_connection, primary_goal) VALUES (?, ?, ?, ?, ?, ?)", [$userId, $duration, $accountability, $resourcesOfInterest, $spiritualConnection, $primaryGoal]);
    }

    public static function updateQuiz($id, $userId, $duration, $accountability, $resourcesOfInterest, $spiritualConnection, $primaryGoal)
    {
        return self::execute("UPDATE quizzes SET user_id = ?, duration = ?, accountability = ?, resources_of_interest = ?, spiritual_connection = ?, primary_goal = ? WHERE quiz_id = ?", [$userId, $duration, $accountability, $resourcesOfInterest, $spiritualConnection, $primaryGoal, $id]);
    }

    public static function deleteQuiz($id)
    {
        return self::execute("DELETE FROM quizzes WHERE quiz_id = ?", [$id]);
    }

    // Resource-specific methods
    public static function getAllResources()
    {
        return self::getRows("SELECT * FROM resources", [], 'Resource');
    }

    public static function getResourceById($id)
    {
        return self::getSingleRow("SELECT * FROM resources WHERE resource_id = ?", [$id], 'Resource');
    }

    public static function addResource($userId, $title, $description, $category, $type, $difficulty, $tags)
    {
        return self::execute("INSERT INTO resources (user_id, title, description, category, type, difficulty, tags) VALUES (?, ?, ?, ?, ?, ?, ?)", [$userId, $title, $description, $category, $type, $difficulty, $tags]);
    }

    public static function updateResource($id, $userId, $title, $description, $category, $type, $difficulty, $tags)
    {
        return self::execute("UPDATE resources SET user_id = ?, title = ?, description = ?, category = ?, type = ?, difficulty = ?, tags = ? WHERE resource_id = ?", [$userId, $title, $description, $category, $type, $difficulty, $tags, $id]);
    }

    public static function deleteResource($id)
    {
        return self::execute("DELETE FROM resources WHERE resource_id = ?", [$id]);
    }

    // Testimony-specific methods
    public static function getAllTestimonies()
    {
        return self::getRows("SELECT * FROM testimonies", [], 'Testimony');
    }

    public static function getTestimonyById($id)
    {
        return self::getSingleRow("SELECT * FROM testimonies WHERE testimony_id = ?", [$id], 'Testimony');
    }

    public static function addTestimony($userId, $quoteText, $citeName, $approved = false)
    {
        return self::execute("INSERT INTO testimonies (user_id, quote_text, cite_name, approved) VALUES (?, ?, ?, ?)", [$userId, $quoteText, $citeName, $approved]);
    }

    public static function updateTestimony($id, $userId, $quoteText, $citeName, $approved)
    {
        return self::execute("UPDATE testimonies SET user_id = ?, quote_text = ?, cite_name = ?, approved = ? WHERE testimony_id = ?", [$userId, $quoteText, $citeName, $approved, $id]);
    }

    public static function deleteTestimony($id)
    {
        return self::execute("DELETE FROM testimonies WHERE testimony_id = ?", [$id]);
    }
}
?>