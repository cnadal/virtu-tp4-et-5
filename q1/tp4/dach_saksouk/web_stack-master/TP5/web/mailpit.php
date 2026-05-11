<?php
require_once 'auth.php';

if (isset($_GET['logout'])) requireLogout();
requireAuth('mailpit.php');

$user = $_SESSION['kc_user'];

// Appel à l'API interne de Mailpit (conteneur mail_test, port 8025)
$mailpitApi = 'http://mail_test:8025/api/v1/messages';
$messages   = [];
$error      = null;

$ctx = stream_context_create([
    'http' => [
        'timeout'       => 3,
        'ignore_errors' => true,
    ],
]);

$raw = @file_get_contents($mailpitApi, false, $ctx);
if ($raw === false) {
    $error = "Impossible de contacter Mailpit (http://mail_test:8025). Vérifiez que le conteneur est démarré.";
} else {
    $data = json_decode($raw, true);
    $messages = $data['messages'] ?? [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TP5 — Mailpit</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; }
        header {
            background: #1e293b; padding: 1rem 2rem;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid #334155;
        }
        header h1 { font-size: 1.3rem; color: #f97316; }
        nav a { color: #94a3b8; text-decoration: none; margin-left: 1rem; font-size: 0.9rem; }
        nav a:hover { color: #e2e8f0; }
        .logout { padding: 0.4rem 0.8rem; background: #ef4444; color: #fff !important; border-radius: 4px; }
        main { max-width: 960px; margin: 2rem auto; padding: 0 1rem; }
        h2 { margin-bottom: 1.2rem; }
        .badge { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem;
                 background: #f97316; color: #fff; margin-left: 0.5rem; vertical-align: middle; }
        .error { background: #450a0a; border: 1px solid #ef4444; padding: 1rem; border-radius: 6px;
                 color: #fca5a5; margin-bottom: 1rem; }
        .empty { text-align: center; padding: 3rem; color: #64748b; }
        .mail-list { list-style: none; }
        .mail-item {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 1rem 1.2rem;
            margin-bottom: 0.8rem;
            cursor: pointer;
            transition: border-color .2s;
        }
        .mail-item:hover { border-color: #f97316; }
        .mail-item .subject { font-weight: 600; color: #f1f5f9; margin-bottom: 0.3rem; }
        .mail-item .meta { font-size: 0.82rem; color: #64748b; }
        .mail-item .from { color: #94a3b8; }
        .mail-body {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 6px;
            padding: 1rem;
            margin-top: 0.6rem;
            font-size: 0.85rem;
            color: #cbd5e1;
            white-space: pre-wrap;
            word-break: break-word;
            display: none;
        }
        .mail-item.open .mail-body { display: block; }
        .count-info { color: #64748b; font-size: 0.85rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
<header>
    <h1>&#x2709;&#xFE0F; Mailpit</h1>
    <nav>
        <a href="/index.php">&#x2302; Accueil</a>
        <a href="/master.php">Master</a>
        <a href="/slave.php">Slave</a>
        <a href="?logout=1" class="logout">Déconnexion</a>
    </nav>
</header>

<main>
    <h2>Boîte de réception <span class="badge">MAILPIT</span></h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($messages)): ?>
        <p class="empty">&#x1F4ED; Aucun e-mail intercepté pour l'instant.</p>
    <?php else: ?>
        <p class="count-info"><?= count($messages) ?> message(s) intercepté(s). Cliquez pour développer.</p>
        <ul class="mail-list">
            <?php foreach ($messages as $msg):
                $subject = $msg['Subject'] ?? '(sans objet)';
                $from    = isset($msg['From']) ? ($msg['From']['Name'] ?? '') . ' <' . ($msg['From']['Address'] ?? '') . '>' : '?';
                $to      = isset($msg['To'][0]) ? ($msg['To'][0]['Address'] ?? '?') : '?';
                $date    = $msg['Date'] ?? '';
                $snippet = $msg['Snippet'] ?? '';
            ?>
            <li class="mail-item" onclick="this.classList.toggle('open')">
                <div class="subject"><?= htmlspecialchars($subject) ?></div>
                <div class="meta">
                    <span class="from">De : <?= htmlspecialchars($from) ?></span>
                    &nbsp;|&nbsp;
                    À : <?= htmlspecialchars($to) ?>
                    &nbsp;|&nbsp;
                    <?= htmlspecialchars($date) ?>
                </div>
                <div class="mail-body"><?= htmlspecialchars($snippet) ?></div>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
</body>
</html>
