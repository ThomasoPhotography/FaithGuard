<?php
require_once '../../database/Database.php';
$posts = Database::getRows("SELECT * FROM posts");
echo json_encode($posts);
?>