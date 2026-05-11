<?php
$host = 'db_mysql';
$db = 'garageM';
$user = 'root';
$pass = 'mdpMysql';
$dsn = "mysql:host=$host;port=3306;dbname=$db;charset=utf8mb4";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<h1>Liste des voitures (MySQL)</h1>";
    $stmt = $pdo->query("SELECT * FROM voitures");
    while ($row = $stmt->fetch()) {
        echo "Immat : " . $row['immatriculation'] . " | Couleur : " . $row['couleur'] . " | KM : " . $row['km'] . "<br>";
    }
    echo "<br><a href='ajoutVoituresGarage.php' style='padding:10px;background:#28a745;color:white;text-decoration:none;border-radius:5px;'>Ajouter voiture</a>";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>