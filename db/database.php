<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/db/config.php';
class Database
{
    const BIBLE_API_KEY = 'jhCTF0KSddJvqBAb-na7pQ'; // Example key, replace with actual key if needed
    const BIBLE_API_BASE_URL = 'https://api.scripture.api.bible/v1';
    public static function getConnection()
    {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            return new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    // ... (rest of the class methods remain unchanged) ...
    public static function getRows($sql, $params = [], $type = null)
    {
        try {
            $conn = self::getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            if ($type == null) {
                $rows = $stmt->fetchAll();
            } else {
                $rows = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $type);
            }
            $conn = null;
            return $rows;
        } catch (PDOException $e) {
            error_log("Query error in getRows: " . $e->getMessage() . " SQL: " . $sql);
            return [];
        }
    }

    public static function getSingleRow($sql, $params = [], $type = null)
    {
        try {
            $conn = self::getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            if ($type == null) {
                $row = $stmt->fetch();
            } else {
                $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $type);
                $row = $stmt->fetch();
            }
            $conn = null;
            return $row;
        } catch (PDOException $e) {
            error_log("Query error in getSingleRow: " . $e->getMessage() . " SQL: " . $sql);
            return false;
        }
    }

    public static function execute($sql, $params = [])
    {
        try {
            $conn = self::getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $aantalRijen = $stmt->rowCount();
            return $aantalRijen;
        } catch (PDOException $e) {
            error_log("Query error in execute: " . $e->getMessage() . " SQL: " . $sql);
            return 0;
        }
    }
}
