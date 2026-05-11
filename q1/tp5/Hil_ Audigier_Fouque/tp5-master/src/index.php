<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TP5</title>
</head>
<body>
<h1>TP5</h1>

<?php if (!isset($_SESSION['user'])): ?>
    <p>Vous n'êtes pas connecté.</p>
    <p><a href="/login.php">Se connecter avec Keycloak</a></p>
<?php else: ?>
    <p>Connecté en tant que <strong><?= htmlspecialchars($_SESSION['user']['preferred_username'] ?? 'utilisateur') ?></strong></p>
    <ul>
        <li><a href="/master.php">Master</a></li>
        <li><a href="/slave.php">Slave</a></li>
        <li><a href="/mailpit.php">Mailpit</a></li>
        <li><a href="/logout.php">Se déconnecter</a></li>
    </ul>
<?php endif; ?>
</body>
</html>