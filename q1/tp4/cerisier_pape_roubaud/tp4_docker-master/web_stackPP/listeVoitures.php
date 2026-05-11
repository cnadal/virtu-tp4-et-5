<?php
$host = 'bddPostgres';
$port = 5432;
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdoPg = new PDO("pgsql:host=$host;port=$port;dbname=garagep", 'postgres', 'mdpRoot23', $options);
    echo "<h2>Voitures Postgres</h2>";
    $stmt = $pdoPg->query("SELECT * FROM voitures");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['immatriculation'] . " - " . $row['puissance'] . "cv - " . $row['prix'] . "€<br>";
    }
} catch (\PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}