<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Init tables</title></head>
<body>
<h1>Initialisation des tables</h1>
<?php
// MySQL
try {
    $pdo = new PDO("mysql:host=" . getenv('MYSQL_HOST') . ";dbname=" . getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
    $pdo->exec("CREATE TABLE IF NOT EXISTS voitures (
        id INT PRIMARY KEY AUTO_INCREMENT,
        immatriculation VARCHAR(20),
        couleur VARCHAR(30),
        km INT
    )");
    echo "<p>MySQL : table voitures créée.</p>";
} catch (Exception $e) {
    echo "<p>MySQL erreur : " . $e->getMessage() . "</p>";
}

// PostgreSQL
try {
    $pdo = new PDO("pgsql:host=postgres_keycloak;dbname=" . getenv('POSTGRES_DB'), getenv('POSTGRES_USER'), getenv('POSTGRES_PASSWORD'));
    $pdo->exec("CREATE TABLE IF NOT EXISTS voitures (
        id SERIAL PRIMARY KEY,
        immatriculation VARCHAR(20),
        couleur VARCHAR(30),
        km INT
    )");
    echo "<p>PostgreSQL : table voitures créée.</p>";
} catch (Exception $e) {
    echo "<p>PostgreSQL erreur : " . $e->getMessage() . "</p>";
}
?>
<a href="index.php">Retour</a>
</body>
</html>