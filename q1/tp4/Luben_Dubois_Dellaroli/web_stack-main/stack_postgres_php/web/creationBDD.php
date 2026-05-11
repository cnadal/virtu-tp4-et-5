<?php
declare(strict_types=1);

const GARAGE_DATABASES = ['garageM', 'garageP'];

function pgPdoAdmin(): PDO
{
    $host = 'postgres';
    $port = 5432;
    $user = 'postgres';
    $password = getenv('POSTGRES_PASSWORD') ?: '';

    $dsn = sprintf('pgsql:host=%s;port=%d;dbname=postgres', $host, $port);

    return new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function pgPdoDatabase(string $database): PDO
{
    $host = 'postgres';
    $port = 5432;
    $user = 'postgres';
    $password = getenv('POSTGRES_PASSWORD') ?: '';

    $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $host, $port, $database);

    return new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function pgIdent(string $name): string
{
    return '"' . str_replace('"', '""', $name) . '"';
}

function pgCreateDatabaseIfMissing(PDO $admin, string $database): void
{
    $check = $admin->prepare('SELECT 1 FROM pg_database WHERE datname = :db');
    $check->execute(['db' => $database]);

    if (!$check->fetchColumn()) {
        $admin->exec('CREATE DATABASE ' . pgIdent($database));
    }
}

function pgCreateVoituresTableIfMissing(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS voitures (
            id SERIAL PRIMARY KEY,
            marque VARCHAR(80) NOT NULL,
            modele VARCHAR(80) NOT NULL,
            couleur VARCHAR(40) NOT NULL,
            annee INTEGER NOT NULL,
            prix NUMERIC(10,2) NOT NULL,
            cree_le TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
        )'
    );
}

function pgEnsureGarageDatabasesAndTables(array $databases = GARAGE_DATABASES): void
{
    $admin = pgPdoAdmin();

    foreach ($databases as $dbName) {
        pgCreateDatabaseIfMissing($admin, $dbName);
        $pdo = pgPdoDatabase($dbName);
        pgCreateVoituresTableIfMissing($pdo);
    }
}
