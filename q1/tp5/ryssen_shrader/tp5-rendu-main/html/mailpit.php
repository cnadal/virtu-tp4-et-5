<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: /index.php"); exit; }
$user = htmlspecialchars($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TP5 - Mailpit</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        h1 { color: #333; }
        iframe { width: 100%; height: 600px; border: 1px solid #ddd; border-radius: 5px; margin-top: 20px; }
        .back { display: inline-block; margin-top: 20px; color: #2196F3; text-decoration: none; }
        .info { color: #666; }
    </style>
</head>
<body>
    <h1>Mailpit - Messagerie de test</h1>
    <p class="info">Connecté : <strong><?= $user ?></strong></p>
    
    <iframe src="http://tp5.local:8025"></iframe>
    
    <a class="back" href="/index.php">← Retour à l'accueil</a>
</body>
</html>
