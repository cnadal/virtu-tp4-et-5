<?php
$host = 'mysql';
$db = 'testdb';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';
$port = 3306;

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->query("SELECT * FROM voitures");
    echo "<h2>Liste des voitures</h2><ul>";
    while ($row = $stmt->fetch()) {
        echo "<li>" . htmlspecialchars($row['immatriculation']) . "</li>";
    }
    echo "</ul>";
} catch (\PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
}
?>