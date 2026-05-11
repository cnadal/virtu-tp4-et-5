<?php
$host = 'db_postgres';
$db   = 'testdb';
$user = 'root';
$pass = 'root';

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

    $stmt = $pdo->query("SELECT * FROM voitures");
    echo "<h1>Garage Postgres</h1>";
    while ($row = $stmt->fetch()) {
        echo "Voiture : " . htmlspecialchars($row['immatriculation']) . "<br>";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>