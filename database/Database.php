<?php
class Database {
    private static function getConnection() {
        $user = 'root';
        $pass = '';
        $host = 'localhost';
        $db_name = 'faithguard_db'; // Update to your DB name
        $dsn = "mysql:host=$host;dbname=$db_name"; // For Postgres: "pgsql:host=$host;dbname=$db_name"
        $db = new PDO($dsn, $user, $pass);
        return $db;
    }
    public static function getRows($sql, $params = [], $type = null) {
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
    }
    public static function getSingleRow($sql, $params = [], $type = null) {
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
    }
    public static function execute($sql, $params = []) {
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $aantalRijen = $stmt->rowCount();
        return $aantalRijen;
    }
}
?>