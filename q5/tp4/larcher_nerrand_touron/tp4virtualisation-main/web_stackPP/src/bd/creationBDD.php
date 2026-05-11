<?php
// Connexion à PostgreSQL (sans préciser de base pour créer la DB)
$host = 'db_postgres';
$port = '5432';
$user = getenv('POSTGRES_USER') ?: 'admin';
$password = getenv('POSTGRES_PASSWORD') ?: 'admin1234';
$dbname = getenv('POSTGRES_DB') ?: 'garage';

try {
    // Connexion sur la base par défaut "postgres" pour créer la nouvelle DB
    $dsn = "pgsql:host=$host;port=$port;dbname=postgres";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Créer la base de données si elle n'existe pas
    $pdo->exec("CREATE DATABASE $dbname");
    echo "<p style='color:green;'>✅ Base de données '$dbname' créée avec succès.</p>";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'already exists')) {
        echo "<p style='color:orange;'>⚠️ La base '$dbname' existe déjà.</p>";
    } else {
        echo "<p style='color:red;'>❌ Erreur création BDD : " . $e->getMessage() . "</p>";
    }
}

// Reconnexion sur la bonne base pour créer la table
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS voitures (
            id SERIAL PRIMARY KEY,
            marque VARCHAR(100) NOT NULL,
            modele VARCHAR(100) NOT NULL,
            annee INT,
            couleur VARCHAR(50),
            prix DECIMAL(10,2)
        )
    ");
    echo "<p style='color:green;'>✅ Table 'voitures' créée avec succès.</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Erreur création table : " . $e->getMessage() . "</p>";
}
?>