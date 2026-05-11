<?php
/**
 * DB Helper - Connexion à la base de données
 */
function getDBConnection($type = 'master') {
    $host = getenv("MYSQL_HOST_" . strtoupper($type)) ?: "mysql_$type";
    $db   = getenv('MYSQL_DATABASE') ?: 'app_db';
    $user = getenv('MYSQL_USER') ?: 'app_user';
    $pass = getenv('MYSQL_PASSWORD') ?: 'app_password';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        return false;
    }
}
