<?php
// /db/database.php
class Database {
    private static function getConnection() {
        $user = 'ID483117_faithguard';
        $pass = 'LowLeague13_';
        $host = 'ID483117_faithguard.db.webhosting.be';
        $db_name = 'ID483117_faithguard';
        
        $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4"; // Added character set
        
        $options = [
            // CRITICAL: Instruct PDO to throw exceptions on errors
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Disable emulated prepared statements for security and stability
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        
        try {
            $db = new PDO($dsn, $user, $pass, $options);
            return $db;
        } catch (PDOException $e) {
            // Log the error and stop execution with a clear message
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection error. Please check server logs for details.");
        }
    }
    
    // --- Added try/catch to core methods to better handle query errors ---

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
            return []; // Return empty array on failure
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
            return false; // Return false on failure
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
            return 0; // Return 0 on failure
        }
    }
}
?>