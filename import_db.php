<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Drop and recreate database
    $pdo->exec("DROP DATABASE IF EXISTS id483117_faithguard");
    $pdo->exec("CREATE DATABASE id483117_faithguard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE id483117_faithguard");

    echo "Database id483117_faithguard recreated successfully!\n";

    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/db/faithguard_db.sql');

    // Remove comments and execute
    $pdo->exec($sql);
    echo "faithguard_db.sql imported successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
