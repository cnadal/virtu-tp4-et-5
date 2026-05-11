<?php
$host = 'mysql_db';
// The user is root because of issues if we don't grant privileges. Let's stick to root
$user = 'root';
$pass = getenv('MYSQL_ROOT_PASSWORD') ?: 'root';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS garageM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Base de données 'garageM' créée ou déjà existante.<br>";

    // Use database
    $pdo->exec("USE garageM;");

    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS voitures (
        id INT AUTO_INCREMENT PRIMARY KEY,
        immatriculation VARCHAR(15) NOT NULL,
        couleur VARCHAR(50) NOT NULL,
        km INT NOT NULL
    )";
    $pdo->exec($sql);
    echo "Table 'voitures' créée ou déjà existante.<br>";

    echo "<br><a href='../ajoutVoituresGarage.php'>Ajouter une voiture</a> | <a href='../listeVoitures.php'>Voir la liste</a>";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
