<?php
$immat = 'XX-' . rand(100,999) . '-XX';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO("mysql:host=mysql;port=3306;dbname=garageM;charset=utf8mb4", 'root', 'mdpRoot23', $options);
    $couleurs = ['rouge','bleu','vert','noir','blanc'];
    $pdo->exec("INSERT INTO voitures VALUES ('$immat', '" . $couleurs[array_rand($couleurs)] . "', " . rand(0, 200000) . ")");
    echo "Ajout : $immat<br>";
} catch (\PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}