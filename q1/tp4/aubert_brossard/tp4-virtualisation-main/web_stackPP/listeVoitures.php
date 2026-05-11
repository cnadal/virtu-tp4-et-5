<?php
$host = 'postgres';
$db   = 'garageP';
$user = 'root';
$pass = 'RootPassword123'; 
$charset = 'utf8';
$port = 5432;

$dsn = "pgsql:host=$host;port=$port;dbname=$db;options='--client_encoding=$charset'";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->query("SELECT * FROM voitures");

    echo "<h1>Liste des voitures (Postgres)</h1>";

    while ($row = $stmt->fetch()) {
        echo "Immatriculation : " . $row['immatriculation'] . "<br>";
        echo "Puissance : " . $row['puissance'] . " cv<br>";
        echo "Prix : " . $row['prix'] . " €<br><br>";
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

