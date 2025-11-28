<?php
require_once '/db/database.php';
$posts = Database::getRows("SELECT * FROM posts");
echo json_encode($posts);
?>