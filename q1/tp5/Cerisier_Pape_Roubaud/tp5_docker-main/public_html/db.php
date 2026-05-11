<?php
function getDbConnection($host, $dbname, $user, $pass) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET NAMES 'utf8mb4'");
        return $pdo;
    } catch(PDOException $e) {
        die("Erreur de connexion à la base de données $host : " . $e->getMessage());
    }
}
?>
