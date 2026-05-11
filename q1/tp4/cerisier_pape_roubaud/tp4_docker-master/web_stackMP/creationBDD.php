<?php
$host = 'mysql';
$port = 3306;
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", 'root', 'mdpRoot23', $options);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS garageM");
    $pdo->exec("USE garageM");
    $pdo->exec("CREATE TABLE IF NOT EXISTS voitures (
        immatriculation VARCHAR(20) PRIMARY KEY,
        couleur VARCHAR(50),
        km INT
    )");
    echo "Base et table OK.";
} catch (\PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
