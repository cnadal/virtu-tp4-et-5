<?php
$host = 'mysql';
$db = 'testdb';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';
$port = 3306;

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$message = "";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $immat = isset($_POST['immatriculation']) ? $_POST['immatriculation'] : '';
        $marque = isset($_POST['marque']) ? $_POST['marque'] : '';
        $modele = isset($_POST['modele']) ? $_POST['modele'] : '';

        if ($immat && $marque && $modele) {
            $stmt = $pdo->prepare(
                "INSERT INTO voitures (immatriculation, marque, modele) VALUES (?, ?, ?)"
            );
            $stmt->execute([$immat, $marque, $modele]);
            $message = "Voiture ajoutée avec succès !";
        } else {
            $message = "Tous les champs sont obligatoires.";
        }
    }
} catch (\PDOException $e) {
    $message = "Erreur PDO : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Ajout voiture</title></head>
<body>
<h2>Ajouter une voiture au garage</h2>
<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>
<form method="POST">
    <label>Immatriculation : <input type="text" name="immatriculation" required></label><br><br>
    <label>Marque :          <input type="text" name="marque" required></label><br><br>
    <label>Modèle :          <input type="text" name="modele" required></label><br><br>
    <button type="submit">Ajouter</button>
</form>
<br><a href="listeVoitures.php">Voir la liste</a>
</body>
</html>