<?php
$mysql_host = 'db_mysql'; // ou 'nom_du_conteneur_mysql' si Docker
$mysql_db = 'garageM';
$mysql_user = 'root';
$mysql_pass = 'mdpRoot23';
$mysql_charset = 'utf8mb4';
$mysql_port = 3306; // ou le port 1103
$mysql_dsn = "mysql:host=$mysql_host;port=$mysql_port;dbname=$mysql_db;charset=$mysql_charset";


$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Erreurs sous forme d'exceptions
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Résultats en tableau associatif
	PDO::ATTR_EMULATE_PREPARES => false, // Préparation réelle des requêtes
];
//try {
$mysql_pdo = new PDO($mysql_dsn, $mysql_user, $mysql_pass, $options);
// Requête de test

//$stmt = $mysql_pdo->query("SELECT * FROM Voitures");
//while ($row = $stmt->fetch()) {
////echo $row['immatriculation'] . "<br>" . $row['couleur'] . "<br>" . $row['km'] . "<br><br>";
//}
//} catch (\PDOException $e) {
//echo "Erreur PDO : " . $e->getMessage();
//}

$immat = $_GET['immatriculation'] ?? null;
$couleur = $_GET['couleur'] ?? null;
$km = $_GET['km'] ?? null;

$requete = "INSERT INTO Voitures (immatriculation, couleur, km) values (" . $immat . ", '" . $couleur . "', " . $km . ");" ?? null;

if (isset($immat) && isset($couleur) && isset($km)) {
	$mysql_pdo->query($requete);
}

echo "<form action='/ajoutVoitureGarage.php'>
	<label for='immatriculation'>Immatriculation</label>
	<input type='number' name='immatriculation'>
	<label for='couleur'>Couleur</label>
	<input type='text' name='couleur'>
	<label for='km'>Kilomètres</label>
	<input type='number' name='km'>
	<input type='submit' value='soumettre'>
	</form>";