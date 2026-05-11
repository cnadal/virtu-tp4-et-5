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

$pdo = dbMaster();
$users = findUsersFromDb($pdo);
$existingUserIds = array_map(static fn(array $user): int => (int) $user['id'], $users);

$errors = [];
$status = (string) (filter_input(INPUT_GET, 'status', FILTER_UNSAFE_RAW) ?: '');
$activeAction = 'create_user';

$userForm = [
    'username' => '',
    'full_name' => '',
];

$carForm = [
    'user_id' => '',
    'brand' => '',
    'model' => '',
    'registration' => '',
    'year' => '',
];

$deleteUserForm = [
    'user_id' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = (string) (filter_input(INPUT_POST, 'form_action', FILTER_UNSAFE_RAW) ?: '');
    $allowedActions = ['create_user', 'create_car', 'delete_user'];
    if (in_array($formAction, $allowedActions, true)) {
        $activeAction = $formAction;
    }

    if ($formAction === 'create_user') {
        $username = trim((string) (filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW) ?: ''));
        $fullName = trim((string) (filter_input(INPUT_POST, 'full_name', FILTER_UNSAFE_RAW) ?: ''));

        $userForm['username'] = $username;
        $userForm['full_name'] = $fullName;

        if ($username === '') {
            $errors[] = 'Le nom utilisateur est obligatoire.';
        }

        if ($fullName === '') {
            $errors[] = 'Le nom complet est obligatoire.';
        }

        if (strlen($username) > 80) {
            $errors[] = 'Le nom utilisateur ne doit pas depasser 80 caracteres.';
        }

        if (strlen($fullName) > 120) {
            $errors[] = 'Le nom complet ne doit pas depasser 120 caracteres.';
        }

        if ($errors === []) {
            try {
                createUserFromDb($pdo, $username, $fullName);
                header('Location: /master.php?status=user_created');
                exit;
            } catch (PDOException $exception) {
                $errors[] = 'Impossible de creer cet utilisateur. Le nom utilisateur existe peut-etre deja.';
            }
        }
    }

    if ($formAction === 'create_car') {
        $ownerIdRaw = filter_input(INPUT_POST, 'car_user_id', FILTER_VALIDATE_INT);
        $brand = trim((string) (filter_input(INPUT_POST, 'brand', FILTER_UNSAFE_RAW) ?: ''));
        $model = trim((string) (filter_input(INPUT_POST, 'model', FILTER_UNSAFE_RAW) ?: ''));
        $registration = strtoupper(trim((string) (filter_input(INPUT_POST, 'registration', FILTER_UNSAFE_RAW) ?: '')));
        $yearRaw = trim((string) (filter_input(INPUT_POST, 'year', FILTER_UNSAFE_RAW) ?: ''));

        $carForm['user_id'] = (string) (filter_input(INPUT_POST, 'car_user_id', FILTER_UNSAFE_RAW) ?: '');
        $carForm['brand'] = $brand;
        $carForm['model'] = $model;
        $carForm['registration'] = $registration;
        $carForm['year'] = $yearRaw;

        if ($ownerIdRaw === false || !in_array($ownerIdRaw, $existingUserIds, true)) {
            $errors[] = 'Selectionnez un proprietaire valide.';
        }

        if ($brand === '' || strlen($brand) > 80) {
            $errors[] = 'La marque est obligatoire (80 caracteres max).';
        }

        if ($model === '' || strlen($model) > 80) {
            $errors[] = 'Le modele est obligatoire (80 caracteres max).';
        }

        if ($registration === '' || strlen($registration) > 20) {
            $errors[] = 'L\'immatriculation est obligatoire (20 caracteres max).';
        }

        $year = null;
        if ($yearRaw !== '') {
            $validatedYear = filter_var($yearRaw, FILTER_VALIDATE_INT);
            $maxYear = (int) date('Y') + 1;

            if ($validatedYear === false || $validatedYear < 1886 || $validatedYear > $maxYear) {
                $errors[] = 'L\'annee doit etre comprise entre 1886 et ' . $maxYear . '.';
            } else {
                $year = $validatedYear;
            }
        }

        if ($errors === []) {
            try {
                createCarFromDb($pdo, $ownerIdRaw, $brand, $model, $registration, $year);
                header('Location: /master.php?status=car_created');
                exit;
            } catch (PDOException $exception) {
                $errors[] = 'Impossible de creer cette voiture. L\'immatriculation existe peut-etre deja.';
            }
        }
    }

    if ($formAction === 'delete_user') {
        $deleteUserId = filter_input(INPUT_POST, 'delete_user_id', FILTER_VALIDATE_INT);
        $deleteUserForm['user_id'] = (string) (filter_input(INPUT_POST, 'delete_user_id', FILTER_UNSAFE_RAW) ?: '');

        if ($deleteUserId === false || !in_array($deleteUserId, $existingUserIds, true)) {
            $errors[] = 'Selectionnez un utilisateur valide a supprimer.';
        }

        if ($errors === []) {
            try {
                deleteUserFromDb($pdo, $deleteUserId);
                header('Location: /master.php?status=user_deleted');
                exit;
            } catch (PDOException $exception) {
                $errors[] = 'Impossible de supprimer cet utilisateur pour le moment.';
            }
        }
    }

    if ($formAction === 'delete_car') {
        $carId = filter_input(INPUT_POST, 'delete_car_id', FILTER_VALIDATE_INT);

        if ($carId === false || $carId <= 0) {
            $errors[] = 'Identifiant de voiture invalide.';
        }

        if ($errors === []) {
            try {
                deleteCarFromDb($pdo, $carId);
                header('Location: /master.php?status=car_deleted');
                exit;
            } catch (PDOException $exception) {
                $errors[] = 'Impossible de supprimer cette voiture pour le moment.';
            }
        }
    }
}

$rawUserId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
$userId = $rawUserId === false ? null : $rawUserId;

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
    <title>Master - Voitures</title>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; margin: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #ddd; padding: 0.5rem; text-align: left; }
        .top { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; margin-bottom: 1rem; }
        .forms { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
        .card { border: 1px solid #ddd; border-radius: 10px; padding: 1rem; }
        .field { display: grid; gap: 0.35rem; margin-bottom: 0.7rem; }
        input, select, button { padding: 0.45rem 0.6rem; border: 1px solid #ccc; border-radius: 6px; }
        .feedback { padding: 0.7rem; border-radius: 8px; margin-bottom: 1rem; }
        .feedback.error { background: #fee2e2; color: #7f1d1d; }
        .feedback.ok { background: #dcfce7; color: #14532d; }
        .action-form { display: none; }
        .action-form.active { display: block; }
        a { color: #0f766e; }
    </style>
</head>
<body>
<div class="top">
    <h1 style="margin:0;">master.php</h1>
    <a href="/index.php">Retour menu</a>
</div>

<?php if ($status === 'user_created'): ?>
    <div class="feedback ok">Utilisateur cree avec succes.</div>
<?php endif; ?>

<?php if ($status === 'car_created'): ?>
    <div class="feedback ok">Voiture ajoutee avec succes.</div>
<?php endif; ?>

<?php if ($status === 'user_deleted'): ?>
    <div class="feedback ok">Utilisateur supprime avec succes.</div>
<?php endif; ?>

<?php if ($status === 'car_deleted'): ?>
    <div class="feedback ok">Voiture supprimee avec succes.</div>
<?php endif; ?>

<?php if ($errors !== []): ?>
    <div class="feedback error">
        <?php foreach ($errors as $error): ?>
            <div><?= h($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="get" action="/master.php" class="top">
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

<section class="forms">
    <div class="card">
        <h2 style="margin-top:0;">Actions</h2>
        <div class="field">
            <label for="action_selector">Choisir une action</label>
            <select id="action_selector" name="action_selector">
                <option value="create_user" <?= $activeAction === 'create_user' ? 'selected' : '' ?>>Creer un utilisateur</option>
                <option value="create_car" <?= $activeAction === 'create_car' ? 'selected' : '' ?>>Creer une voiture</option>
                <option value="delete_user" <?= $activeAction === 'delete_user' ? 'selected' : '' ?>>Supprimer un utilisateur</option>
            </select>
        </div>
    </div>

    <form method="post" action="/master.php" class="card action-form <?= $activeAction === 'create_user' ? 'active' : '' ?>" data-action="create_user">
        <h2 style="margin-top:0;">Ajouter un utilisateur</h2>
        <input type="hidden" name="form_action" value="create_user">
        <div class="field">
            <label for="username">Nom utilisateur</label>
            <input id="username" name="username" maxlength="80" required value="<?= h($userForm['username']) ?>">
        </div>
        <div class="field">
            <label for="full_name">Nom complet</label>
            <input id="full_name" name="full_name" maxlength="120" required value="<?= h($userForm['full_name']) ?>">
        </div>
        <button type="submit">Creer l'utilisateur</button>
    </form>

    <form method="post" action="/master.php" class="card action-form <?= $activeAction === 'create_car' ? 'active' : '' ?>" data-action="create_car">
        <h2 style="margin-top:0;">Ajouter une voiture</h2>
        <input type="hidden" name="form_action" value="create_car">
        <div class="field">
            <label for="car_user_id">Proprietaire</label>
            <select id="car_user_id" name="car_user_id" required>
                <option value="">Selectionner...</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= (int) $user['id'] ?>" <?= $carForm['user_id'] === (string) $user['id'] ? 'selected' : '' ?>>
                        <?= h($user['full_name']) ?> (<?= h($user['username']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="brand">Marque</label>
            <input id="brand" name="brand" maxlength="80" required value="<?= h($carForm['brand']) ?>">
        </div>
        <div class="field">
            <label for="model">Modele</label>
            <input id="model" name="model" maxlength="80" required value="<?= h($carForm['model']) ?>">
        </div>
        <div class="field">
            <label for="registration">Immatriculation</label>
            <input id="registration" name="registration" maxlength="20" required value="<?= h($carForm['registration']) ?>">
        </div>
        <div class="field">
            <label for="year">Annee (optionnel)</label>
            <input id="year" name="year" type="number" min="1886" max="<?= (int) date('Y') + 1 ?>" value="<?= h($carForm['year']) ?>">
        </div>
        <button type="submit">Ajouter la voiture</button>
    </form>

    <form method="post" action="/master.php" class="card action-form <?= $activeAction === 'delete_user' ? 'active' : '' ?>" data-action="delete_user">
        <h2 style="margin-top:0;">Supprimer un utilisateur</h2>
        <input type="hidden" name="form_action" value="delete_user">
        <div class="field">
            <label for="delete_user_id">Utilisateur</label>
            <select id="delete_user_id" name="delete_user_id" required>
                <option value="">Selectionner...</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= (int) $user['id'] ?>" <?= $deleteUserForm['user_id'] === (string) $user['id'] ? 'selected' : '' ?>>
                        <?= h($user['full_name']) ?> (<?= h($user['username']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button
            type="submit"
            onclick="return confirm('Supprimer cet utilisateur ? Ses voitures seront aussi supprimees.')"
        >
            Supprimer l'utilisateur
        </button>
    </form>
</section>

<table>
    <thead>
    <tr>
        <th>Proprietaire</th>
        <th>Marque</th>
        <th>Modele</th>
        <th>Immatriculation</th>
        <th>Annee</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($cars === []): ?>
        <tr><td colspan="6">Aucune voiture trouvee.</td></tr>
    <?php else: ?>
        <?php foreach ($cars as $car): ?>
            <tr>
                <td><?= h($car['owner']) ?></td>
                <td><?= h($car['brand']) ?></td>
                <td><?= h($car['model']) ?></td>
                <td><?= h($car['registration']) ?></td>
                <td><?= h((string) $car['year']) ?></td>
                <td>
                    <form method="post" action="/master.php" style="margin:0;">
                        <input type="hidden" name="form_action" value="delete_car">
                        <input type="hidden" name="delete_car_id" value="<?= (int) $car['id'] ?>">
                        <button
                            type="submit"
                            onclick="return confirm('Supprimer cette voiture ?')"
                        >
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<script>
const actionSelector = document.getElementById('action_selector');
const actionForms = document.querySelectorAll('.action-form');

function updateActionForms(selectedAction) {
    actionForms.forEach((form) => {
        form.classList.toggle('active', form.dataset.action === selectedAction);
    });
}

if (actionSelector) {
    updateActionForms(actionSelector.value);
    actionSelector.addEventListener('change', (event) => {
        updateActionForms(event.target.value);
    });
}
</script>
</body>
</html>
