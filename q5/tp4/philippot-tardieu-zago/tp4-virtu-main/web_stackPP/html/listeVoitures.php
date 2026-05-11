<?php
$pgsql_host = 'db_postgres';
$pgsql_port = 5432;
$pgsql_db = 'postgres';
$pgsql_user = 'postgres';
$pgsql_password = 'mdpPSecreT';

$pgsql_dsn = "pgsql:host=$pgsql_host;port=$pgsql_port;dbname=$pgsql_db;";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Erreurs sous forme d'exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Résultats en tableau associatif
    PDO::ATTR_EMULATE_PREPARES => false, // Préparation réelle des requêtes
];

try {
    $pgsql_pdo = new PDO($pgsql_dsn, $pgsql_user, $pgsql_password, $options);
    // Requête de test
    $stmt = $pgsql_pdo->query("SELECT * FROM Voitures");
    while ($row = $stmt->fetch()) {
        echo "Immatriculation : " . $row['immatriculation'] . "<br>Prix : " . $row['prix'] . "<br>Puissance : " . $row['puissance'] . "<br><br>";
    }
} catch (\PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
}

?>