<?php
require_once __DIR__ . '/auth.php';

try {
    $pdo = new PDO('mysql:host=mysql_master;dbname=garage;charset=utf8', 'appuser', 'apppass');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM voitures");
    $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur master : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Master</title>
</head>
<body>
<h1>BDD Master</h1>
<p><a href="/index.php">Retour</a></p>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Marque</th>
        <th>Modèle</th>
        <th>Année</th>
    </tr>
    <?php foreach ($voitures as $v): ?>
        <tr>
            <td><?= htmlspecialchars($v['id']) ?></td>
            <td><?= htmlspecialchars($v['marque']) ?></td>
            <td><?= htmlspecialchars($v['modele']) ?></td>
            <td><?= htmlspecialchars($v['annee']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>