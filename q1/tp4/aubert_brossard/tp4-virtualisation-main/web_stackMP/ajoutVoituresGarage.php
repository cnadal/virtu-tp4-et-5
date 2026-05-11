<?php
$host = 'mysql';
$db   = 'garageM';
$user = 'root';
$pass = 'RootPassword123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Génération aléatoire
    $immatriculation = strtoupper(substr(md5(rand()), 0, 6));
    $couleurs = ['rouge', 'bleu', 'noir', 'blanc'];
    $couleur = $couleurs[array_rand($couleurs)];
    $km = rand(0, 200000);

    $sql = "INSERT INTO voitures (immatriculation, couleur, km) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$immatriculation, $couleur, $km]);

    echo "Voiture ajoutée : $immatriculation";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
