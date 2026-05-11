<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/car_repository.php';
require_once __DIR__ . '/lib/db.php';

ensureSessionStarted();

if (!isAuthenticated()) {
    header('Location: /?action=login');
    exit;
}

$rawUserId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
$userId = $rawUserId === false ? null : $rawUserId;

$pdo = dbSlave();
$users = findUsersFromDb($pdo);
$cars = findCarsByUserFromDb($pdo, $userId);

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slave - Voitures</title>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; margin: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #ddd; padding: 0.5rem; text-align: left; }
        .top { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; margin-bottom: 1rem; }
        a { color: #0f766e; }
    </style>
</head>
<body>
<div class="top">
    <h1 style="margin:0;">slave.php</h1>
    <a href="/index.php">Retour menu</a>
</div>
<form method="get" class="top">
    <label for="user_id">Filtrer :</label>
    <select id="user_id" name="user_id">
        <option value="">Tous</option>
        <?php foreach ($users as $user): ?>
            <option value="<?= (int) $user['id'] ?>" <?= $userId === (int) $user['id'] ? 'selected' : '' ?>>
                <?= h($user['full_name']) ?> (<?= h($user['username']) ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Appliquer</button>
</form>
<table>
    <thead>
    <tr>
        <th>Proprietaire</th>
        <th>Marque</th>
        <th>Modele</th>
        <th>Immatriculation</th>
        <th>Annee</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($cars === []): ?>
        <tr><td colspan="5">Aucune voiture trouvee.</td></tr>
    <?php else: ?>
        <?php foreach ($cars as $car): ?>
            <tr>
                <td><?= h($car['owner']) ?></td>
                <td><?= h($car['brand']) ?></td>
                <td><?= h($car['model']) ?></td>
                <td><?= h($car['registration']) ?></td>
                <td><?= h((string) $car['year']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>
