<?php
$host = 'mysql';
$db   = 'garageM';
$user = 'root';
$pass = 'RootPassword123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->query("SELECT * FROM voitures");

    echo "<h1>Liste des voitures</h1>";

    while ($row = $stmt->fetch()) {
        echo "Immatriculation : " . $row['immatriculation'] . "<br>";
        echo "Couleur : " . $row['couleur'] . "<br>";
        echo "Km : " . $row['km'] . "<br><br>";
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
