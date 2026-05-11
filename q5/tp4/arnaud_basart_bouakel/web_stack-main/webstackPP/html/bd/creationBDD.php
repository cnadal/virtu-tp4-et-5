<?php
$host = 'db_pg';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=postgres", $user, $pass);
    $pdo->exec("CREATE DATABASE garageM");

    $pdo = new PDO("pgsql:host=$host;dbname=garageM", $user, $pass);
    $pdo->exec("CREATE TABLE IF NOT EXISTS voitures (
        id SERIAL PRIMARY KEY,
        immatriculation VARCHAR(20),
        couleur VARCHAR(20),
        km INTEGER
    )");

} catch (Exception $e) {
    echo $e->getMessage();
}
?>