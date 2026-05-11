<?php
$immat = 'XX-' . rand(100,999) . '-XX';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdoPg = new PDO("pgsql:host=bddPostgres;port=5432;dbname=garagep", 'postgres', 'mdpRoot23', $options);
    $pdoPg->exec("INSERT INTO voitures VALUES ('$immat', " . rand(60,300) . ", " . rand(5000, 50000) . ")");
    echo "Ajout : $immat<br>";
} catch (\PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}