<?php
$host = 'postgres_db';
$user = getenv('POSTGRES_USER') ?: 'admin';
$pass = getenv('POSTGRES_PASSWORD') ?: '1234';

try {
    $pdo_setup = new PDO("pgsql:host=$host;dbname=postgres", $user, $pass);
    $pdo_setup->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
        $pdo_setup->exec("CREATE DATABASE garageP");
        echo "Base de données 'garageP' créée.<br>";
    } catch (PDOException $e) {
        echo "Base de données 'garageP' déjà existante.<br>";
    }

    $pdo = new PDO("pgsql:host=$host;dbname=garageP", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS voitures (
        id SERIAL PRIMARY KEY,
        immatriculation VARCHAR(15) NOT NULL,
        couleur VARCHAR(50) NOT NULL,
        km INT NOT NULL
    )";
    $pdo->exec($sql);
    echo "Table 'voitures' créée ou déjà existante.<br>";

    echo "<br><a href='../ajoutVoituresGarage.php'>Ajouter une voiture</a> | <a href='../listeVoitures.php'>Voir la liste</a>";
} catch (PDOException $e) {
    echo "Erreur (Postgres) : " . $e->getMessage();
}
?>
