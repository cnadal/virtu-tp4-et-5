<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Garage TP5</title></head>
<body>
<h1>Garage TP5</h1>
<?php
try {
    new PDO("mysql:host=" . getenv('MYSQL_HOST') . ";dbname=" . getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
    echo "<p>MySQL : OK</p>";
} catch (Exception $e) {
    echo "<p>MySQL : " . $e->getMessage() . "</p>";
}

try {
    new PDO("pgsql:host=postgres_keycloak;dbname=" . getenv('POSTGRES_DB'), getenv('POSTGRES_USER'), getenv('POSTGRES_PASSWORD'));
    echo "<p>PostgreSQL : OK</p>";
} catch (Exception $e) {
    echo "<p>PostgreSQL : " . $e->getMessage() . "</p>";
}
?>
<a href="creationBDD.php">Initialiser les tables</a> |
<a href="ajoutVoituresGarage.php">Ajouter une voiture</a> |
<a href="listeVoitures.php">Liste des voitures</a>
</body>
</html>