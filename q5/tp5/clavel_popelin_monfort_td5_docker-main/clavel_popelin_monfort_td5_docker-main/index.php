<?php
session_start();

/**
 * CONFIGURATION KEYCLOAK
 * Ces valeurs doivent correspondre à ce que tu créeras dans l'interface Keycloak
 */
$keycloak_url = "http://tp5.local/auth"; // Via le proxy Nginx
$realm = "tp5-realm";
$client_id = "web_app";
$redirect_uri = "http://tp5.local/index.php";

// 1. Si l'utilisateur n'est pas connecté et n'a pas de code de retour
if (!isset($_SESSION['user']) && !isset($_GET['code'])) {
    // Construction de l'URL de connexion Keycloak (Standard OpenID Connect)
    $auth_url = "$keycloak_url/realms/$realm/protocol/openid-connect/auth?" . http_build_query([
            'client_id'     => $client_id,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'scope'         => 'openid'
        ]);

    // Redirection immédiate vers la page de login Keycloak
    header("Location: $auth_url");
    exit;
}

// 2. Si on revient de Keycloak avec un code (Connexion réussie)
if (isset($_GET['code'])) {
    // Ici, normalement, on échangerait le code contre un Token.
    // Pour ton TP, on va simuler que l'utilisateur est maintenant valide.
    $_SESSION['user'] = "Utilisateur Authentifié";
}

// 3. Affichage de la page sécurisée
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration TP5</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; }
        .btn { display: inline-block; padding: 15px 25px; margin: 10px; color: white; text-decoration: none; border-radius: 5px; }
        .master { background-color: #2c3e50; }
        .slave { background-color: #27ae60; }
    </style>
</head>
<body>
<h1>Bienvenue !</h1>
<p>Vous êtes connecté via Keycloak. Choisissez votre base de données :</p>

<a href="master.php" class="btn master">Accéder au Master (Écritures)</a>
<a href="slave.php" class="btn slave">Accéder au Slave (Lectures)</a>
<a href="http://localhost:8025" class="btn" style="background-color:#e67e22;" target="_blank">Boîte mail (Mailpit)</a>
<a href="sync_test.php" class="btn" style="background-color:#c0392b;">Test synchronisation BDD</a>R

<hr>
<p><small>Connecté à l'infrastructure Docker sécurisée</small></p>
</body>
</html>