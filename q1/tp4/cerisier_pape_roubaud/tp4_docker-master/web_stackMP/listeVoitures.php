<?php
$host = 'mysql';
$port = 3306;
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=garageM;charset=utf8mb4", 'root', 'mdpRoot23', $options);
    echo "<h2>Voitures MySQL</h2>";
    $stmt = $pdo->query("SELECT * FROM voitures");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['immatriculation'] . " - " . $row['couleur'] . " - " . $row['km'] . " km<br>";
    }
} catch (\PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}