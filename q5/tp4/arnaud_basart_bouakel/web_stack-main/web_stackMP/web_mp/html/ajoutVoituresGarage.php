<?php
$host = 'db_mysql';
$db = 'garageM';
$user = 'root';
$pass = 'root';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

$immat = chr(rand(65,90)).chr(rand(65,90))."-".rand(100,999)."-".chr(rand(65,90));
$couleurs = array('Rouge','Bleu','Noir','Gris','Blanc');
$couleur = $couleurs[array_rand($couleurs)];
$km = rand(0,200000);

$stmt = $pdo->prepare("INSERT INTO voitures (immatriculation, couleur, km) VALUES (?, ?, ?)");
$stmt->execute(array($immat, $couleur, $km));

echo "Ajouté : $immat - $couleur - $km km<br>";
echo "<a href='index.php'>Voir liste</a>";
?>