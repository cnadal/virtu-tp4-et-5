<?php
$host = 'db_pg';
$db = 'garageM';
$user = 'root';
$pass = 'root';

$pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM voitures");

echo "<h1>Postgres Voitures</h1>";
echo "<table border=1 style='border-collapse:collapse'>";
echo "<tr><th>ID</th><th>Immat</th><th>Couleur</th><th>KM</th></tr>";
while ($row = $stmt->fetch()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['immatriculation'] . "</td>";
    echo "<td>" . $row['couleur'] . "</td>";
    echo "<td>" . $row['km'] . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><a href='ajoutVoituresGarage.php'>Ajouter</a>";
?>