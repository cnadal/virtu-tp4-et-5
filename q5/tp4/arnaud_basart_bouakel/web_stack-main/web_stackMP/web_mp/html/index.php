<?php
$host = 'db_mysql';
$db = 'garageM';
$user = 'root';
$pass = 'root';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$stmt = $pdo->query("SELECT * FROM voitures");

echo "<h1>Voitures</h1>";
echo "<table border=1>";
echo "<tr><th>ID</th><th>Immatriculation</th><th>Couleur</th><th>KM</th></tr>";
while($row = $stmt->fetch()) {
    echo "<tr>";
    echo "<td>".$row['id']."</td>";
    echo "<td>".$row['immatriculation']."</td>";
    echo "<td>".$row['couleur']."</td>";
    echo "<td>".$row['km']."</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><a href='ajoutVoituresGarage.php'>Ajouter voiture</a>";
?>