<?php
session_start();
if (!isset($_SESSION['authenticated'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Mails - Mailpit</title></head>
<body style="margin:0;">
    <div style="padding: 10px; background: #eee; font-family: sans-serif;">
        <a href="index.php">← Retour Accueil</a> | <span>Interface Mailpit (Port 8025)</span>
    </div>
    <!-- Redirection via Iframe sur l'interface de Mailpit exposée -->
    <iframe src="http://localhost:8025" style="width:100%; height:90vh; border:none;"></iframe>
</body>
</html>
