<?php
$host = 'postgres';
$user = 'postgres';
$pass = 'postgres';

try {
    // Connexion au serveur sans spécifier de base pour pouvoir en créer une
    $pdo = new PDO("pgsql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connexion réussie à PostgreSQL.<br>";

    // Création de la base de données
    $dbName = "garage";
    $sql = "CREATE DATABASE $dbName";
    
    // Postgres ne permet pas de créer la BDD s'il y a déjà une transaction active,
    // on l'exécute directement.
    try {
        $pdo->exec($sql);
        echo "Base de données '$dbName' créée avec succès.<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "La base de données '$dbName' existe déjà.<br>";
        } else {
            throw $e;
        }
    }

    // Se reconnecter à la nouvelle base pour créer la table
    $pdoGarage = new PDO("pgsql:host=$host;dbname=$dbName", $user, $pass);
    $pdoGarage->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlTable = "CREATE TABLE IF NOT EXISTS voitures (
        id SERIAL PRIMARY KEY,
        marque VARCHAR(50) NOT NULL,
        modele VARCHAR(50) NOT NULL,
        annee INTEGER NOT NULL
    )";
    $pdoGarage->exec($sqlTable);
    echo "Table 'voitures' créée avec succès.<br>";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
