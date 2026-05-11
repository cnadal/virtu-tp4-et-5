<?php
$host = 'postgres'; 
$user = 'root';
$pass = 'RootPassword123'; 
$charset = 'utf8';
$port = 5432;

$dsn = "pgsql:host=$host;port=$port;dbname=postgres;options='--client_encoding=$charset'";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdo->exec("CREATE DATABASE \"garageP\"");

    echo "Base garageP créée (ou déjà existante).<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') === false) {
        echo "Erreur création BDD : " . $e->getMessage();
        exit;
    } else {
        echo "Base garageP déjà existante.<br>";
    }
}

$dsnGarage = "pgsql:host=$host;port=$port;dbname=garageP;options='--client_encoding=$charset'";

try {
    $pdoGarage = new PDO($dsnGarage, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdoGarage->exec("
        CREATE TABLE IF NOT EXISTS voitures (
            immatriculation VARCHAR(50) PRIMARY KEY,
            puissance INT,
            prix INT
        )
    ");

    echo "Table voitures créée avec succès dans garageP.";
} catch (PDOException $e) {
    echo "Erreur création table : " . $e->getMessage();
}
?>

