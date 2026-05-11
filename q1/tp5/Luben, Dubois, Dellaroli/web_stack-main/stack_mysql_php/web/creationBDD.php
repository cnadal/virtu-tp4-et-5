<?php
declare(strict_types=1);

const GARAGE_DATABASES = ['garageM', 'garageP'];

function mysqlPdoAdmin(): PDO
{
    $host = 'mysql';
    $port = 3306;
    $user = 'root';
    $password = getenv('MYSQL_ROOT_PASSWORD') ?: '';

    $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port);

    return new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function mysqlPdoDatabase(string $database): PDO
{
    $host = 'mysql';
    $port = 3306;
    $user = 'root';
    $password = getenv('MYSQL_ROOT_PASSWORD') ?: '';

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $database);

    return new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function mysqlIdent(string $name): string
{
    return '`' . str_replace('`', '``', $name) . '`';
}

function mysqlCreateDatabaseIfMissing(PDO $admin, string $database): void
{
    $admin->exec('CREATE DATABASE IF NOT EXISTS ' . mysqlIdent($database) . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
}

function mysqlCreateVoituresTableIfMissing(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS voitures (
            id INT AUTO_INCREMENT PRIMARY KEY,
            marque VARCHAR(80) NOT NULL,
            modele VARCHAR(80) NOT NULL,
            couleur VARCHAR(40) NOT NULL,
            annee INT NOT NULL,
            prix DECIMAL(10,2) NOT NULL,
            cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
}

function mysqlEnsureGarageDatabasesAndTables(array $databases = GARAGE_DATABASES): void
{
    $admin = mysqlPdoAdmin();

    foreach ($databases as $dbName) {
        mysqlCreateDatabaseIfMissing($admin, $dbName);
        $pdo = mysqlPdoDatabase($dbName);
        mysqlCreateVoituresTableIfMissing($pdo);
    }
}
