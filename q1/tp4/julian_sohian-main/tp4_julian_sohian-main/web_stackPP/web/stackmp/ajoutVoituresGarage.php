<?php
$pdo = new PDO("mysql:host=mysql;dbname=garage", "user", "password");
$pdo->exec("CREATE TABLE IF NOT EXISTS voitures(id INT AUTO_INCREMENT PRIMARY KEY, nom VARCHAR(50))");
$pdo->exec("INSERT INTO voitures(nom) VALUES('BMW')");
echo "Voiture ajoutée";
?>
