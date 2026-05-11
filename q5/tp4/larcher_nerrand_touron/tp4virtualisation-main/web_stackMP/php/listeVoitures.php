<?php

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=db_mysql;dbname=voitures_db;charset=utf8", 'prod_user', 'MotDePasse321');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour récupérer toutes les voitures
    $stmt = $pdo->query("SELECT * FROM voitures");
    $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des voitures</title>
</head>
<body>
    <h1>Liste des voitures</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Année</th>
                <th>Immatriculation</th>
                <th>Couleur</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($voitures as $voiture): ?>
                <tr>
                    <td><?= htmlspecialchars($voiture['id']) ?></td>
                    <td><?= htmlspecialchars($voiture['marque']) ?></td>
                    <td><?= htmlspecialchars($voiture['modele']) ?></td>
                    <td><?= htmlspecialchars($voiture['annee']) ?></td>
                    <td><?= htmlspecialchars($voiture['immatriculation']) ?></td>
                    <td><?= htmlspecialchars($voiture['couleur']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
