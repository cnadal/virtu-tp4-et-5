<?php
session_start();

// Si pas connecté → rediriger vers auth.php
if (!isset($_SESSION['user'])) {
    header('Location: /auth.php');
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<body>
  <h1>Bienvenue <?= htmlspecialchars($user['preferred_username']) ?></h1>
    <h3> Cloud est le meilleur </h3>
</body>
</html>
