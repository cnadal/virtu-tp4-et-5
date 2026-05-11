<?php
$host = 'mysql';
$user = 'root';
$pass = 'RootPassword123';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Création BDD
    $pdo->exec("CREATE DATABASE IF NOT EXISTS garageM");

    // Utilisation de la BDD
    $pdo->exec("USE garageM");

    // Création table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS voitures (
            immatriculation VARCHAR(50) PRIMARY KEY,
            couleur VARCHAR(50),
            km INT
        )
    ");

    echo "BDD et table créées avec succès";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
