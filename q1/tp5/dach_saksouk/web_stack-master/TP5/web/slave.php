<?php
require_once 'auth.php';

if (isset($_GET['logout'])) requireLogout();
requireAuth('slave.php');

$user = $_SESSION['kc_user'];

// Connexion MySQL Slave
$host = 'mysql_slave';
$db   = getenv('MYSQL_DATABASE') ?: 'garage';
$u    = getenv('MYSQL_USER')     ?: 'appuser';
$p    = getenv('MYSQL_PASSWORD') ?: '';

$voitures = [];
$error    = null;

try {
    $pdo  = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $u, $p, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3,
    ]);
    $stmt = $pdo->query('SELECT * FROM voitures ORDER BY id');
    $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TP5 — MySQL Slave</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; }
        header {
            background: #1e293b; padding: 1rem 2rem;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid #334155;
        }
        header h1 { font-size: 1.3rem; color: #22c55e; }
        nav a { color: #94a3b8; text-decoration: none; margin-left: 1rem; font-size: 0.9rem; }
        nav a:hover { color: #e2e8f0; }
        .logout { padding: 0.4rem 0.8rem; background: #ef4444; color: #fff !important; border-radius: 4px; }
        main { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        h2 { margin-bottom: 1.5rem; }
        .badge { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem;
                 background: #22c55e; color: #fff; margin-left: 0.5rem; vertical-align: middle; }
        .error { background: #450a0a; border: 1px solid #ef4444; padding: 1rem; border-radius: 6px; color: #fca5a5; margin-bottom: 1rem; }
        table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 8px; overflow: hidden; }
        th { background: #0f172a; padding: 0.8rem 1rem; text-align: left; color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 0.8rem 1rem; border-top: 1px solid #334155; }
        tr:hover td { background: #0f172a; }
        .empty { text-align: center; padding: 2rem; color: #64748b; }
        .info { background: #172554; border: 1px solid #1d4ed8; padding: 0.8rem 1rem;
                border-radius: 6px; color: #93c5fd; margin-bottom: 1rem; font-size: 0.9rem; }
    </style>
</head>
<body>
<header>
    <h1>&#x1F4EC; MySQL Slave</h1>
    <nav>
        <a href="/index.php">&#x2302; Accueil</a>
        <a href="/master.php">Master</a>
        <a href="/mailpit.php">Mailpit</a>
        <a href="?logout=1" class="logout">Déconnexion</a>
    </nav>
</header>

<main>
    <h2>Voitures — Base Slave <span class="badge">SLAVE</span></h2>

    <div class="info">
        &#x2139;&#xFE0F; Cette page lit depuis <strong>mysql_slave</strong>.
        Les données sont répliquées automatiquement depuis le master.
        Supprimez une ligne sur le master : elle doit disparaître ici aussi.
    </div>

    <?php if ($error): ?>
        <div class="error"><strong>Erreur de connexion :</strong> <?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($voitures)): ?>
        <p class="empty">Aucune voiture trouvée.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Marque</th><th>Modèle</th><th>Année</th><th>Couleur</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($voitures as $v): ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><?= htmlspecialchars($v['marque']) ?></td>
                    <td><?= htmlspecialchars($v['modele']) ?></td>
                    <td><?= htmlspecialchars($v['annee']) ?></td>
                    <td><?= htmlspecialchars($v['couleur']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="margin-top:.8rem;color:#64748b;font-size:.85rem;"><?= count($voitures) ?> voiture(s) trouvée(s).</p>
    <?php endif; ?>
</main>
</body>
</html>
