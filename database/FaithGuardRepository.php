<?php
require_once dirname(__FILE__) . "/Database.php";
class FaithGuardRepository {
    public static function getUserByEmail($email) {
        return Database::getSingleRow("SELECT * FROM users WHERE email = ?", [$email]);
    }
    public static function createUser($email, $passwordHash) {
        return Database::execute("INSERT INTO users (email, password_hash) VALUES (?, ?)", [$email, $passwordHash]);
    }
    // Add more methods as needed, e.g., for posts, resources
}
?>