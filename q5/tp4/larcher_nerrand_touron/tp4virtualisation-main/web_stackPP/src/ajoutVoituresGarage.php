<?php
$host = 'db_postgres';
$port = '5432';
$user = getenv('POSTGRES_USER') ?: 'admin';
$password = getenv('POSTGRES_PASSWORD') ?: 'admin1234';
$dbname = getenv('POSTGRES_DB') ?: 'garage';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $stmt = $pdo->prepare("
            INSERT INTO voitures (marque, modele, annee, couleur, prix)
            VALUES (:marque, :modele, :annee, :couleur, :prix)
        ");
        $stmt->execute([
            ':marque'  => htmlspecialchars($_POST['marque']),
            ':modele'  => htmlspecialchars($_POST['modele']),
            ':annee'   => (int)$_POST['annee'],
            ':couleur' => htmlspecialchars($_POST['couleur']),
            ':prix'    => (float)$_POST['prix'],
        ]);
        $message = "<p style='color:green;'>✅ Voiture ajoutée avec succès !</p>";
    } catch (PDOException $e) {
        $message = "<p style='color:red;'>❌ Erreur : " . $e->getMessage() . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout Voiture - Garage</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 40px auto; }
        input { display: block; width: 100%; padding: 8px; margin: 6px 0 14px; box-sizing: border-box; }
        button { background: #0066cc; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; }
        button:hover { background: #004fa3; }
    </style>
</head>
<body>
    <h1>🚗 Ajouter une voiture</h1>
    <?= $message ?>
    <form method="POST">
        <label>Marque :</label>
        <input type="text" name="marque" required placeholder="ex: Renault">

        <label>Modèle :</label>
        <input type="text" name="modele" required placeholder="ex: Clio">

        <label>Année :</label>
        <input type="number" name="annee" required placeholder="ex: 2021" min="1900" max="2099">

        <label>Couleur :</label>
        <input type="text" name="couleur" placeholder="ex: Rouge">

        <label>Prix (€) :</label>
        <input type="number" name="prix" step="0.01" placeholder="ex: 12500.00">

        <button type="submit">Ajouter</button>
    </form>
    <p><a href="/web_stackPP/listeVoitures.php">📋 Voir la liste des voitures</a></p>
</body>
</html>