<?php
header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // For AJAX calls later

$host = 'mysql_db';
$db = 'garageM';
$user = 'root';
$pass = getenv('MYSQL_ROOT_PASSWORD') ?: 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT * FROM voitures ORDER BY id DESC");

    echo "<h1>Liste des voitures stockées (MySQL)</h1>";
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr style='background-color:#f2f2f2'><th>ID</th><th>Immatriculation</th><th>Couleur</th><th>Kilométrage</th></tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['immatriculation']}</td>";
        echo "<td>{$row['couleur']}</td>";
        echo "<td>{$row['km']} km</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><a href='ajoutVoituresGarage.php' style='padding:10px;background:#007BFF;color:white;text-decoration:none;border-radius:5px;'>Ajouter une voiture aléatoire</a>";
} catch (PDOException $e) {
    echo "Erreur (assurez-vous d'avoir créé la BDD): " . $e->getMessage();
    echo "<br><a href='bd/creationBDD.php'>Créer la BDD</a>";
}
?>
