<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Allow specific methods
header('Access-Control-Allow-Headers: Content-Type'); // Allow specific headers

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

try {
	$mysql_pdo = new PDO($mysql_dsn, $mysql_user, $mysql_pass, $options);

	// Get JSON data from the request body
	$json = file_get_contents('php://input');
	$data = json_decode($json, true);

	$immat = $data['immatriculation'] ?? null;
	$couleur = $data['couleur'] ?? null;
	$km = $data['km'] ?? null;

	if (isset($immat) && isset($couleur) && isset($km)) {
		// Use prepared statements to prevent SQL injection
		$stmt = $mysql_pdo->prepare("INSERT INTO Voitures (immatriculation, couleur, km) VALUES (:immat, :couleur, :km)");
		$stmt->bindParam(':immat', $immat);
		$stmt->bindParam(':couleur', $couleur);
		$stmt->bindParam(':km', $km);
		$stmt->execute();

		echo json_encode(['success' => true, 'message' => 'Car added successfully']);
	} else {
		echo json_encode(['error' => 'Missing required fields']);
	}
} catch (\PDOException $e) {
	echo json_encode(['error' => 'Erreur PDO : ' . $e->getMessage()]);
}
