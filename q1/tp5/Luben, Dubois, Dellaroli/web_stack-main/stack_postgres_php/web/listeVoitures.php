<?php
declare(strict_types=1);

require_once __DIR__ . '/creationBDD.php';

$databases = GARAGE_DATABASES;
$results = [];

try {
    pgEnsureGarageDatabasesAndTables($databases);

    foreach ($databases as $dbName) {
        try {
            $pdo = pgPdoDatabase($dbName);

            $stmt = $pdo->query(
                'SELECT id, marque, modele, couleur, annee, prix, cree_le
                 FROM voitures
                 ORDER BY id ASC'
            );
            $results[$dbName] = [
                'rows' => $stmt->fetchAll(),
                'error' => null,
            ];
        } catch (Throwable $e) {
            $results[$dbName] = [
                'rows' => [],
                'error' => $e->getMessage(),
            ];
        }
    }
} catch (Throwable $e) {
    foreach ($databases as $dbName) {
        $results[$dbName] = [
            'rows' => [],
            'error' => $e->getMessage(),
        ];
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste voitures</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 2rem; }
        th, td { border: 1px solid #ddd; padding: 0.5rem; text-align: left; }
        th { background: #f7f7f7; }
        .ko { color: #b00; }
    </style>
</head>
<body>
    <h1>Voitures par base de donnees</h1>

    <?php foreach ($results as $dbName => $data): ?>
        <h2><?= htmlspecialchars($dbName, ENT_QUOTES, 'UTF-8') ?></h2>

        <?php if ($data['error'] !== null): ?>
            <p class="ko">Erreur: <?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php elseif (count($data['rows']) === 0): ?>
            <p>Aucun tuple dans la table voitures.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Marque</th>
                        <th>Modele</th>
                        <th>Couleur</th>
                        <th>Annee</th>
                        <th>Prix</th>
                        <th>Cree le</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['rows'] as $row): ?>
                        <tr>
                            <td><?= (int) $row['id'] ?></td>
                            <td><?= htmlspecialchars((string) $row['marque'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $row['modele'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $row['couleur'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int) $row['annee'] ?></td>
                            <td><?= number_format((float) $row['prix'], 2, '.', ' ') ?></td>
                            <td><?= htmlspecialchars((string) $row['cree_le'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>

    <p><a href="ajoutVoituresGarage.php">Ajouter des voitures aleatoires</a></p>
</body>
</html>
