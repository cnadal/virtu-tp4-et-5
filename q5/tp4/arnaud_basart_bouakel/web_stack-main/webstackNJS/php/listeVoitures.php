<?php
$host = 'db_mysql';
$db = 'garageM';
$user = 'root';
$pass = 'root';
$dsn = "mysql:host=$host;port=3306;dbname=$db;charset=utf8mb4";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    echo "<h1>Liste des voitures (GarageM)</h1>";
    $stmt = $pdo->query("SELECT * FROM voitures ORDER BY id DESC");
    $count = $stmt->rowCount();

    if ($count == 0) {
        echo "<p>Aucune voiture dans le garage</p>";
    } else {
        echo "<p><strong>Total : $count voiture(s)</strong></p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>
                <tr style='background: #007bff; color: white;'>
                    <th>ID</th>
                    <th>Immatriculation</th>
                    <th>Couleur</th>
                    <th>Km</th>
                </tr>";

        while ($row = $stmt->fetch()) {
            echo "<tr style='text-align: center;'>
                    <td>{$row['id']}</td>
                    <td>{$row['immatriculation']}</td>
                    <td style='background: " . strtolower($row['couleur']) . "; font-weight: bold;'>{$row['couleur']}</td>
                    <td>{$row['km']}</td>
                  </tr>";
        }
        echo "</table>";
    }
} catch (PDOException $e) {
    echo "<h2>Erreur : " . $e->getMessage() . "</h2>";
}
?>