<?php
$pdo = new PDO("mysql:host=mysql", "user", "password");
$pdo->exec("CREATE DATABASE IF NOT EXISTS garage");
echo "BDD créée";
?>
