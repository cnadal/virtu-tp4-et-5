<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Liste des voitures</title></head>
<body>
<h1>Liste des voitures</h1>

<h2>MySQL</h2>
<?php
try {
    $pdo = new PDO("mysql:host=" . getenv('MYSQL_HOST') . ";dbname=" . getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
    $rows = $pdo->query("SELECT * FROM voitures ORDER BY id DESC")->fetchAll();
    if ($rows) {
        echo "<table border=1><tr><th>ID</th><th>Immatriculation</th><th>Couleur</th><th>KM</th></tr>";
        foreach ($rows as $r) {
            echo "<tr><td>{$r['id']}</td><td>{$r['immatriculation']}</td><td>{$r['couleur']}</td><td>{$r['km']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Aucune voiture.</p>";
    }
} catch (Exception $e) {
    echo "<p>Erreur MySQL : " . $e->getMessage() . "</p>";
}
?>

<h2>PostgreSQL</h2>
<?php
try {
    $pdo = new PDO("pgsql:host=postgres_keycloak;dbname=" . getenv('POSTGRES_DB'), getenv('POSTGRES_USER'), getenv('POSTGRES_PASSWORD'));
    $rows = $pdo->query("SELECT * FROM voitures ORDER BY id DESC")->fetchAll();
    if ($rows) {
        echo "<table border=1><tr><th>ID</th><th>Immatriculation</th><th>Couleur</th><th>KM</th></tr>";
        foreach ($rows as $r) {
            echo "<tr><td>{$r['id']}</td><td>{$r['immatriculation']}</td><td>{$r['couleur']}</td><td>{$r['km']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Aucune voiture.</p>";
    }
} catch (Exception $e) {
    echo "<p>Erreur PostgreSQL : " . $e->getMessage() . "</p>";
}
?>

<br><a href="ajoutVoituresGarage.php">Ajouter une voiture</a> | <a href="index.php">Accueil</a>
</body>
</html>