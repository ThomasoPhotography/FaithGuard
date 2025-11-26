<?php
require_once '../../database/Database.php';
$resources = Database::getRows("SELECT * FROM resources");
echo json_encode($resources);
?>