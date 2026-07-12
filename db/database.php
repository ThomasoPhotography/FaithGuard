<?php
require_once __DIR__ . '/config.php';
class Database
{
    const BIBLE_API_KEY      = 'jhCTF0KSddJvqBAb-na7pQ'; // Example key, replace with actual key if needed
    const BIBLE_API_BASE_URL = 'https://api.scripture.api.bible/v1';
    public static function getConnection()
    {
        $host    = defined('DB_HOST') ? DB_HOST : 'localhost';
        $dbName  = defined('DB_NAME') ? DB_NAME : '';
        $user    = defined('DB_USER') ? DB_USER : '';
        $pass    = defined('DB_PASS') ? DB_PASS : '';
        $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
        $port    = defined('DB_PORT') ? DB_PORT : '3306';

        $hosts = [];
        if ($host !== '') {
            $hosts[] = $host;
        }
        if ($host !== 'localhost' && $host !== '127.0.0.1') {
            $hosts[] = 'localhost';
            $hosts[] = '127.0.0.1';
        }

        $hostCandidates = [];
        if ($host !== '') {
            $hostCandidates[] = $host;
        }
        if ($host !== 'localhost' && $host !== '127.0.0.1') {
            $hostCandidates[] = 'localhost';
            $hostCandidates[] = '127.0.0.1';
        }
        $hostCandidates[] = 'com-linweb938.srv.combell-ops.net';
        $hostCandidates[] = 'ID483117_faithguard.db.webhosting.be';
        $hostCandidates   = array_values(array_unique($hostCandidates));

        $lastError = null;
        foreach ($hostCandidates as $candidateHost) {
            try {
                $dsn     = 'mysql:host=' . $candidateHost . ';port=' . $port . ';dbname=' . $dbName . ';charset=' . $charset;
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_TIMEOUT            => 5,
                ];
                return new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                $lastError = $e;
            }
        }

        error_log('Database connection failed: ' . $lastError->getMessage());
        throw new Exception('Database connection failed');
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
        } catch (Throwable $e) {
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
        } catch (Throwable $e) {
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
        } catch (Throwable $e) {
            error_log("Query error in execute: " . $e->getMessage() . " SQL: " . $sql);
            return 0;
        }
    }
}
