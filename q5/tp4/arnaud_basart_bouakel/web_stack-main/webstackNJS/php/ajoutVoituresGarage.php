<?php
$host = 'db_mysql';
$db = 'garageM';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $immat = chr(rand(65,90)).chr(rand(65,90))."-".rand(100,999)."-".chr(rand(65,90)).chr(rand(65,90));
    $couleurs = array('Rouge','Bleu','Noir','Gris','Blanc');
    $couleur = $couleurs[array_rand($couleurs)];
    $km = rand(0,200000);

    $stmt = $pdo->prepare("INSERT INTO voitures (immatriculation, couleur, km) VALUES (?, ?, ?)");
    $stmt->execute(array($immat, $couleur, $km));

    echo "<h2>Voiture ajoutée !</h2>";
    echo "<p><strong>Immatriculation :</strong> $immat<br>";
    echo "<strong>Couleur :</strong> $couleur<br>";
    echo "<strong>Km :</strong> $km km</p>";

} catch (Exception $e) {
    echo "<h2>Erreur ajout : " . $e->getMessage() . "</h2>";
}
?>