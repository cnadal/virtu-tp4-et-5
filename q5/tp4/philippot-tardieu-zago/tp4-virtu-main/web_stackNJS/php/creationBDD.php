<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$mysql_host = 'db_mysql';
$mysql_user = 'root';
$mysql_pass = 'mdpRoot23';
$mysql_charset = 'utf8mb4';
$mysql_port = 3306;

// First, connect to the server without specifying a database
$dsn = "mysql:host=$mysql_host;port=$mysql_port;charset=$mysql_charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_MULTI_STATEMENTS => true, // Allow multiple statements
];

try {
    // Connect to the server
    $pdo = new PDO($dsn, $mysql_user, $mysql_pass, $options);

    // Drop the database if it exists
    $pdo->exec("DROP DATABASE IF EXISTS garageM");

    // Create the database
    $pdo->exec("CREATE DATABASE garageM");

    // Now connect to the newly created database
    $dsn_with_db = "mysql:host=$mysql_host;port=$mysql_port;dbname=garageM;charset=$mysql_charset";
    $pdo = new PDO($dsn_with_db, $mysql_user, $mysql_pass, $options);

    // Create the table
    $pdo->exec("
        CREATE TABLE `Voitures` (
            `immatriculation` int NOT NULL,
            `couleur` varchar(20) NOT NULL,
            `km` int NOT NULL
        )
    ");

    // Insert data
    $pdo->exec("
        INSERT INTO `Voitures` (`immatriculation`, `couleur`, `km`) VALUES
        (111222333, 'rouge', 50000),
        (123456789, 'verte', 200000)
    ");

    // Add primary key
    $pdo->exec("ALTER TABLE `Voitures` ADD PRIMARY KEY (`immatriculation`)");

    // Commit (though not strictly necessary for these operations)
    $pdo->exec("COMMIT");

    echo json_encode(['success' => true, 'message' => 'Database and table created successfully']);
} catch (\PDOException $e) {
    echo json_encode(['error' => 'Erreur PDO : ' . $e->getMessage()]);
}
