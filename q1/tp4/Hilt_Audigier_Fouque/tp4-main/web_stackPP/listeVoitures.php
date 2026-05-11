<?php
$host = 'postgres';
$db   = 'garage';
$user = 'postgres';
$pass = 'postgres';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT id, marque, modele, annee FROM voitures";
    $stmt = $pdo->query($sql);

    echo "<h1>Liste des voitures du garage</h1>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Année</th>
            </tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['marque']}</td>
                <td>{$row['modele']}</td>
                <td>{$row['annee']}</td>
              </tr>";
    }
    echo "</table>";
    echo "<br><a href='ajoutVoituresGarage.php'>Ajouter d'autres voitures</a>";

} catch (PDOException $e) {
    echo "Erreur d'affichage : " . $e->getMessage();
}
?>
