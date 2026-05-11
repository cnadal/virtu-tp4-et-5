<?php
declare(strict_types=1);

require_once __DIR__ . '/creationBDD.php';

function randomCar(): array
{
    $marques = ['Renault', 'Peugeot', 'Citroen', 'Toyota', 'Ford', 'BMW'];
    $modeles = ['Clio', '208', 'C3', 'Yaris', 'Focus', 'Serie 1'];
    $couleurs = ['rouge', 'bleu', 'noir', 'blanc', 'gris', 'vert'];

    return [
        'marque' => $marques[array_rand($marques)],
        'modele' => $modeles[array_rand($modeles)],
        'couleur' => $couleurs[array_rand($couleurs)],
        'annee' => random_int(2008, 2026),
        'prix' => random_int(7000, 45000),
    ];
}

function insertRandomCars(PDO $pdo, int $count): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO voitures (marque, modele, couleur, annee, prix)
         VALUES (:marque, :modele, :couleur, :annee, :prix)'
    );

    for ($i = 0; $i < $count; $i++) {
        $car = randomCar();
        $stmt->execute($car);
    }

    return $count;
}

$databases = GARAGE_DATABASES;
$inserted = [];
$error = null;

try {
    pgEnsureGarageDatabasesAndTables($databases);

    foreach ($databases as $dbName) {
        $pdo = pgPdoDatabase($dbName);

        $nb = random_int(2, 6);
        $inserted[$dbName] = insertRandomCars($pdo, $nb);
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajout voitures</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        .ok { color: #0a6; }
        .ko { color: #b00; }
    </style>
</head>
<body>
    <h1>Ajout aleatoire de voitures</h1>

    <?php if ($error !== null): ?>
        <p class="ko">Erreur: <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php else: ?>
        <p class="ok">Insertion terminee.</p>
        <ul>
            <?php foreach ($inserted as $dbName => $count): ?>
                <li><?= htmlspecialchars($dbName, ENT_QUOTES, 'UTF-8') ?>: <?= (int) $count ?> tuple(s) ajoute(s)</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="listeVoitures.php">Voir la liste des voitures</a></p>
</body>
</html>
