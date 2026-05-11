<?php
// Ce fichier crée la base et la table — à exécuter une seule fois
$host = 'mysql';
$user = 'root';
$pass = 'root';
$port = 3306;

try {
    // Connexion SANS dbname pour pouvoir créer la base
    $pdo = new PDO(
        "mysql:host=$host;port=$port;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->exec("CREATE DATABASE IF NOT EXISTS testdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Base de données <strong>testdb</strong> créée (ou déjà existante).<br>";

    $pdo->exec("USE testdb;");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS voitures (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            immatriculation VARCHAR(20)  NOT NULL UNIQUE,
            marque          VARCHAR(50)  NOT NULL,
            modele          VARCHAR(50)  NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Table <strong>voitures</strong> créée (ou déjà existante).<br>";

} catch (\PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
}
?>