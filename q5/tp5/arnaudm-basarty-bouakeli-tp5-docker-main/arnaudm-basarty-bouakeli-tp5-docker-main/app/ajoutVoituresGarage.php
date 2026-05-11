<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Ajouter une voiture</title></head>
<body>
<h1>Ajouter une voiture</h1>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $immat  = $_POST['immatriculation'];
    $couleur = $_POST['couleur'];
    $km     = (int)$_POST['km'];
    $db     = $_POST['db'];

    try {
        if ($db === 'postgres') {
            $pdo = new PDO("pgsql:host=postgres_keycloak;dbname=" . getenv('POSTGRES_DB'), getenv('POSTGRES_USER'), getenv('POSTGRES_PASSWORD'));
        } else {
            $pdo = new PDO("mysql:host=" . getenv('MYSQL_HOST') . ";dbname=" . getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
        }
        $stmt = $pdo->prepare("INSERT INTO voitures (immatriculation, couleur, km) VALUES (?, ?, ?)");
        $stmt->execute([$immat, $couleur, $km]);
        echo "<p>Voiture $immat ajoutée dans " . strtoupper($db) . ".</p>";
    } catch (Exception $e) {
        echo "<p>Erreur : " . $e->getMessage() . "</p>";
    }
}
?>
<form method="post">
    <label>Immatriculation : <input type="text" name="immatriculation" required></label><br><br>
    <label>Couleur :
        <select name="couleur">
            <option>Blanc</option><option>Noir</option><option>Gris</option>
            <option>Rouge</option><option>Bleu</option><option>Vert</option>
        </select>
    </label><br><br>
    <label>Kilométrage : <input type="number" name="km" value="0" min="0" required></label><br><br>
    <label>Base de données :
        <select name="db">
            <option value="mysql">MySQL</option>
            <option value="postgres">PostgreSQL</option>
        </select>
    </label><br><br>
    <button type="submit">Ajouter</button>
</form>
<br><a href="listeVoitures.php">Voir la liste</a> | <a href="index.php">Accueil</a>
</body>
</html>