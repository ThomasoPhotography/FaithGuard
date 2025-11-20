<?php

class Database
{

    private static function getConnection()
    {
        // set up for using PDO
        $user = 'root';
        $pass = '';
        $host = 'localhost';
        // vul de juiste database naam in (nadat je die eerst hebt geïmporteerd in je phpMyAdmin)
        $db_name = 'faithguard_db';
        // set up DSN
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
            $rows = $stmt->fetchAll(); // // geeft een array terug met een named-array in
        } else {
            $rows = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $type); // geeft een array terug met objecten van een klasse
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
            $row = $stmt->fetch(); // geeft 1 object terug van een klasse
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

}
?>