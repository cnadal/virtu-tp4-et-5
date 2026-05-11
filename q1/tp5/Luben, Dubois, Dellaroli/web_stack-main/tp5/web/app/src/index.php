<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/car_repository.php';
require_once __DIR__ . '/lib/auth.php';

ensureSessionStarted();

$authError = null;
$action = (string) (filter_input(INPUT_GET, 'action', FILTER_UNSAFE_RAW) ?: '');

try {
    if ($action === 'login') {
        header('Location: ' . buildLoginUrl());
        exit;
    }

    if ($action === 'callback') {
        $code = (string) (filter_input(INPUT_GET, 'code', FILTER_UNSAFE_RAW) ?: '');
        $state = (string) (filter_input(INPUT_GET, 'state', FILTER_UNSAFE_RAW) ?: '');

        if ($code === '' || $state === '') {
            throw new RuntimeException('Parametres de callback manquants.');
        }

        handleAuthCallback($code, $state);
        header('Location: /');
        exit;
    }

    if ($action === 'logout') {
        $logoutUrl = logoutUser();
        header('Location: ' . $logoutUrl);
        exit;
    }
} catch (Throwable $exception) {
    $authError = $exception->getMessage();
}

$user = currentUser();
$isAuthenticated = isAuthenticated();

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
    <title>TP5 - Menu</title>
    <style>
        :root {
            --bg: #f6f7fb;
            --text: #1f2937;
            --card: #ffffff;
            --accent: #0f766e;
            --border: #d1d5db;
        }

        body {
            margin: 0;
            font-family: "DejaVu Sans", sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        h1 {
            margin-top: 0;
        }

        form {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        select,
        button {
            padding: 0.55rem 0.7rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
        }

        button {
            background: var(--accent);
            color: #fff;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 0.65rem;
            border-bottom: 1px solid var(--border);
        }

        th {
            font-weight: 600;
            background: #f9fafb;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <?php if ($isAuthenticated): ?>
            <strong>Connecte:</strong>
            <?= h((string) ($user['name'] ?? 'Utilisateur')) ?>
            (<?= h((string) ($user['username'] ?? 'user')) ?>)
            <a style="margin-left: 1rem; color: var(--accent);" href="/?action=logout">Se deconnecter</a>
        <?php else: ?>
            <strong>Authentification requise.</strong>
            <a style="margin-left: 1rem; color: var(--accent);" href="/?action=login">Se connecter avec Keycloak</a>
        <?php endif; ?>

        <?php if ($authError !== null): ?>
            <p style="color: #991b1b; margin-top: 0.8rem;"><?= h($authError) ?></p>
        <?php endif; ?>
    </div>

    <?php if ($isAuthenticated): ?>
    <div class="card">
        <h1>Menu TP5</h1>
        <p>Selectionnez une page :</p>
        <ul>
            <li><a href="/mailpit.php">mailpit.php</a></li>
            <li><a href="/smtp_test.php">smtp_test.php</a></li>
            <li><a href="/master.php">master.php</a></li>
            <li><a href="/slave.php">slave.php</a></li>
        </ul>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
