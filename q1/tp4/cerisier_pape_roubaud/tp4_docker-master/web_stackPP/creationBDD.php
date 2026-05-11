<?php
$host = 'bddPostgres';
$port = 5432;
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=postgres", 'postgres', 'mdpRoot23', $options); 
    try {
        $pdo->exec("CREATE DATABASE garagep");
        echo "Base de données garagep créée avec succès.<br>";
    } catch (\PDOException $e) {
        if ($e->getCode() == '42P04') {
            echo "Base garagep deja la.<br>";
        } else {
            throw $e;
        }
    }
    $pdoGarage = new PDO("pgsql:host=$host;port=$port;dbname=garagep", 'postgres', 'mdpRoot23', $options);
    $pdoGarage->exec("CREATE TABLE IF NOT EXISTS voitures (
        immatriculation VARCHAR(20) PRIMARY KEY,
        puissance INT,
        prix INT
    )");
} catch (\PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
