<?php
$host = 'postgres';
$db   = 'garageP'; 
$user = 'root';
$pass = 'RootPassword123';
$charset = 'utf8';
$port = 5432;

$dsn = "pgsql:host=$host;port=$port;dbname=$db;options='--client_encoding=$charset'";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $immatriculation = strtoupper(substr(md5(rand()), 0, 6));
    $puissance = rand(80, 300);
    $prix = rand(10000, 50000);

    $sql = "INSERT INTO voitures (immatriculation, puissance, prix) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$immatriculation, $puissance, $prix]);

    echo "Voiture ajoutée : $immatriculation (puissance: $puissance, prix: $prix €)";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

