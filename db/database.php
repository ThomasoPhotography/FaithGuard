<?php
class Database {
    private static function getConnection() {
        // NOTE: Credentials provided by user. Ensure the password is correct.
        $user = 'ID483117_faithguard';
        $pass = 'LowLeague13_'; 
        $host = 'ID483117_faithguard.db.webhosting.be';
        $db_name = 'ID483117_faithguard';
        
        $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
        
        $options = [
            // CRITICAL FIX: Set a low connection timeout (e.g., 5 seconds)
            PDO::ATTR_TIMEOUT => 5, 
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        
        try {
            $db = new PDO($dsn, $user, $pass, $options);
            return $db;
        } catch (PDOException $e) {
            // Log the error and stop execution with a clear message
            error_log("Database connection failed: " . $e->getMessage());
            die("DATABASE_CONNECTION_FAILED: " . $e->getMessage()); 
        }
    }
    
    // ... (rest of the class methods remain unchanged) ...
    public static function getRows($sql, $params = [], $type = null) {
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
    
    public static function getSingleRow($sql, $params = [], $type = null) {
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
    
    public static function execute($sql, $params = []) {
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