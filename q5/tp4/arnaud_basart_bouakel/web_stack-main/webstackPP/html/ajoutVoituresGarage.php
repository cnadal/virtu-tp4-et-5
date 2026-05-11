<?php
$host = 'db_pg';
$db = 'garageM';
$user = 'root';
$pass = 'root';

$pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
$immat = chr(rand(65, 90)) . chr(rand(65, 90)) . "-" . rand(100, 999) . "-" . chr(rand(65, 90));
$couleurs = array('Rouge', 'Bleu', 'Noir', 'Gris', 'Blanc');
$couleur = $couleurs[array_rand($couleurs)];
$km = rand(0, 200000);
$stmt = $pdo->prepare("INSERT INTO voitures (immatriculation, couleur, km) VALUES (?, ?, ?)");
$result = $stmt->execute(array($immat, $couleur, $km));
echo "<br><a href='index.php'>Liste</a>";
?>