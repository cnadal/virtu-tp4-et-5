<?php

$host = 'db_postgres';
$db   = 'testdb';
$user = 'root';
$pass = 'root';

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $sql = "CREATE TABLE IF NOT EXISTS voitures (
        id SERIAL PRIMARY KEY,
        immatriculation VARCHAR(20) NOT NULL,
        marque VARCHAR(50)
    );";

    $pdo->exec($sql);
    echo "Base de données prête et table 'voitures' créée !";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>