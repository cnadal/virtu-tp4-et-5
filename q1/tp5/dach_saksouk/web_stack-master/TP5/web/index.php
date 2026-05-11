<?php
require_once 'auth.php';

// Gestion déconnexion
if (isset($_GET['logout'])) {
    requireLogout();
}

// Authentification Keycloak (flux OIDC)
requireAuth('index.php');

$user = $_SESSION['kc_user'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TP5 — Tableau de bord</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
        }
        header {
            background: #1e293b;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #334155;
        }
        header h1 { font-size: 1.4rem; color: #38bdf8; }
        .user-info { font-size: 0.9rem; color: #94a3b8; }
        .user-info strong { color: #e2e8f0; }
        .logout-btn {
            margin-left: 1rem;
            padding: 0.4rem 0.8rem;
            background: #ef4444;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        main {
            max-width: 900px;
            margin: 3rem auto;
            padding: 0 1rem;
        }
        .welcome {
            text-align: center;
            margin-bottom: 3rem;
        }
        .welcome h2 { font-size: 2rem; color: #f1f5f9; margin-bottom: 0.5rem; }
        .welcome p  { color: #94a3b8; }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }
        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 1.5rem;
            text-decoration: none;
            color: inherit;
            transition: border-color .2s, transform .2s;
            display: block;
        }
        .card:hover {
            border-color: #38bdf8;
            transform: translateY(-3px);
        }
        .card .icon { font-size: 2.5rem; margin-bottom: 0.8rem; }
        .card h3 { color: #f1f5f9; margin-bottom: 0.4rem; }
        .card p  { color: #94a3b8; font-size: 0.9rem; }
        .badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            margin-top: 0.6rem;
        }
        .badge-blue   { background: #0ea5e9; color: #fff; }
        .badge-green  { background: #22c55e; color: #fff; }
        .badge-orange { background: #f97316; color: #fff; }
    </style>
</head>
<body>
<header>
    <h1>&#x1F433; Infrastructure TP5</h1>
    <div class="user-info">
        Connecté en tant que <strong><?= htmlspecialchars($user) ?></strong>
        <a href="?logout=1" class="logout-btn">Déconnexion</a>
    </div>
</header>

<main>
    <div class="welcome">
        <h2>Bienvenue, <?= htmlspecialchars($user) ?>&nbsp;!</h2>
        <p>Vous êtes authentifié via Keycloak (SSO). Choisissez un module ci-dessous.</p>
    </div>

    <div class="cards">
        <a href="/master.php" class="card">
            <div class="icon">&#x1F4E5;</div>
            <h3>MySQL Master</h3>
            <p>Affiche la liste des voitures depuis la base <strong>master</strong> (écritures).</p>
            <span class="badge badge-blue">net_db</span>
        </a>

        <a href="/slave.php" class="card">
            <div class="icon">&#x1F4EC;</div>
            <h3>MySQL Slave</h3>
            <p>Affiche la liste des voitures depuis le <strong>slave</strong> (réplication).</p>
            <span class="badge badge-green">net_db</span>
        </a>

        <a href="/mailpit.php" class="card">
            <div class="icon">&#x2709;&#xFE0F;</div>
            <h3>Mailpit</h3>
            <p>Interface de visualisation des e-mails interceptés par Mailpit.</p>
            <span class="badge badge-orange">net_public</span>
        </a>
    </div>
</main>
</body>
</html>
