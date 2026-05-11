<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: /index.php"); exit; }
$user = htmlspecialchars($_SESSION['user']);

$host = 'mysql_slave';
$db = 'app_db';
$dbuser = 'app_user';
$dbpass = 'apppass123';

$error = null;
$voitures = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT * FROM voitures");
    $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TP5 - Base Slave</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; max-width: 800px; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #4CAF50; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        .back { display: inline-block; margin-top: 20px; color: #2196F3; text-decoration: none; }
        .error { color: red; background: #ffe0e0; padding: 10px; border-radius: 5px; }
        .info { color: #666; }
    </style>
</head>
<body>
    <h1>Voitures - Base SLAVE</h1>
    <p class="info">Connecté : <strong><?= $user ?></strong> | Serveur : <strong><?= $host ?></strong></p>
    
    <?php if ($error): ?>
        <p class="error">Erreur : <?= htmlspecialchars($error) ?></p>
    <?php elseif (empty($voitures)): ?>
        <p>Aucune voiture trouvée.</p>
    <?php else: ?>
        <table>
            <tr><th>ID</th><th>Marque</th><th>Modèle</th><th>Année</th><th>Couleur</th></tr>
            <?php foreach ($voitures as $v): ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><?= htmlspecialchars($v['marque']) ?></td>
                    <td><?= htmlspecialchars($v['modele']) ?></td>
                    <td><?= $v['annee'] ?></td>
                    <td><?= htmlspecialchars($v['couleur']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
    <a class="back" href="/index.php">← Retour à l'accueil</a>
</body>
</html>
