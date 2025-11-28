<?php
require_once '/db/database.php';
$resources = Database::getRows("SELECT * FROM resources");
echo json_encode($resources);
?>