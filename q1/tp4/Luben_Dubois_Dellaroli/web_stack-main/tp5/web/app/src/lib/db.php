<?php

declare(strict_types=1);

function dbFromHost(string $host): PDO
{
    $port = getenv('DATABASE_PORT') ?: '3306';
    $database = getenv('DATABASE_DATABASE') ?: '';
    $username = getenv('DATABASE_USER') ?: '';
    $password = getenv('DATABASE_PASSWORD') ?: '';

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DATABASE_HOST') ?: 'mysql_master';
    $pdo = dbFromHost($host);

    return $pdo;
}

function dbMaster(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DATABASE_HOST') ?: 'mysql_master';
    $pdo = dbFromHost($host);

    return $pdo;
}

function dbSlave(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DATABASE_HOST_SLAVE') ?: 'mysql_slave';
    $pdo = dbFromHost($host);

    return $pdo;
}
