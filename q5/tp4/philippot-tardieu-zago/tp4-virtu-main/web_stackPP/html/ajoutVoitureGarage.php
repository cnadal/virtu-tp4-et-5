<?php
$pgsql_host = 'db_postgres';
$pgsql_port = 5432;
$pgsql_db = 'postgres';
$pgsql_user = 'postgres';
$pgsql_password = 'mdpPSecreT';

$pgsql_dsn = "pgsql:host=$pgsql_host;port=$pgsql_port;dbname=$pgsql_db;";

$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Erreurs sous forme d'exceptions
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Résultats en tableau associatif
	PDO::ATTR_EMULATE_PREPARES => false, // Préparation réelle des requêtes
];

$pgsql_pdo = new PDO($pgsql_dsn, $pgsql_user, $pgsql_password, $options);

$immat = $_GET['immatriculation'] ?? null;
$prix = $_GET['prix'] ?? null;
$puissance = $_GET['puissance'] ?? null;

$requete = "INSERT INTO Voitures (immatriculation, prix, puissance) values (" . $immat . ", " . $prix . ", " . $puissance . ");" ?? null;

if (isset($immat) && isset($prix) && isset($puissance)) {
	$pgsql_pdo->query($requete);
}

echo "<form action='/ajoutVoitureGarage.php'>
	<label for='immatriculation'>Immatriculation</label>
	<input type='number' name='immatriculation'>
	<label for='prix'>Prix</label>
	<input type='number' name='prix'>
	<label for='puissance'>Puissance</label>
	<input type='number' name='puissance'>
	<input type='submit' value='soumettre'>
	</form>";

?>