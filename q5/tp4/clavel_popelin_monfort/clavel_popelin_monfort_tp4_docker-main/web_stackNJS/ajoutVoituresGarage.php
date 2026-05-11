<?php

$host = 'db_postgres';
$db   = 'testdb';
$user = 'root';
$pass = 'root';

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass);

    $sql = "INSERT INTO voitures (immatriculation, marque) VALUES ('XX-123-YY', 'Postgres Mobile')";
    $pdo->exec($sql);

    echo "Voiture ajoutée avec succès !";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>