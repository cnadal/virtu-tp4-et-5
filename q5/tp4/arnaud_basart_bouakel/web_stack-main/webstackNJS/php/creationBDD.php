<?php
$host = 'db_mysql';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=3306;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS garageM");
    $pdo->exec("USE garageM");
    $pdo->exec("CREATE TABLE IF NOT EXISTS voitures (
        id INT PRIMARY KEY AUTO_INCREMENT,
        immatriculation VARCHAR(20),
        couleur VARCHAR(20),
        km INT
    )");

    echo "<h2>table voitures créées !</h2>";
} catch (Exception $e) {
    echo $e->getMessage() . "</h2>";
}
?>