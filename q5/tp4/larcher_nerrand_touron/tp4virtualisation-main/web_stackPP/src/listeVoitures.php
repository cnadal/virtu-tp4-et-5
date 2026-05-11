<?php
$host = 'db_postgres';
$port = '5432';
$user = getenv('POSTGRES_USER') ?: 'admin';
$password = getenv('POSTGRES_PASSWORD') ?: 'admin1234';
$dbname = getenv('POSTGRES_DB') ?: 'garage';

$voitures = [];
$erreur = '';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query("SELECT * FROM voitures ORDER BY id DESC");
    $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erreur = "❌ Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des voitures - Garage</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0066cc; color: white; padding: 10px; }
        td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
        .empty { color: #888; font-style: italic; }
    </style>
</head>
<body>
    <h1>🚗 Liste des voitures du garage</h1>

    <?php if ($erreur): ?>
        <p style="color:red;"><?= $erreur ?></p>
    <?php elseif (empty($voitures)): ?>
        <p class="empty">Aucune voiture enregistrée pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>Année</th>
                    <th>Couleur</th>
                    <th>Prix (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($voitures as $v): ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><?= htmlspecialchars($v['marque']) ?></td>
                    <td><?= htmlspecialchars($v['modele']) ?></td>
                    <td><?= $v['annee'] ?></td>
                    <td><?= htmlspecialchars($v['couleur']) ?></td>
                    <td><?= number_format($v['prix'], 2, ',', ' ') ?> €</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong><?= count($voitures) ?> voiture(s) trouvée(s)</strong></p>
    <?php endif; ?>

    <p><a href="/web_stackPP/ajoutVoituresGarage.php">➕ Ajouter une voiture</a></p>
</body>
</html>