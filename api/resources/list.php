<?php
require_once __DIR__ . '../../db/database.php';
$resources = Database::getRows("SELECT * FROM resources");
echo json_encode($resources);
?>