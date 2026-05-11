<?php
session_start();

// Configuration Keycloak
$keycloak_url = "http://tp5.local/auth";
$realm = "tp5";
$client_id = "tp5-app";
$redirect_uri = "http://tp5.local/index.php";

// Si l'utilisateur clique sur déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: $keycloak_url/realms/$realm/protocol/openid-connect/logout?redirect_uri=" . urlencode($redirect_uri));
    exit;
}

// Si on reçoit un code d'autorisation de Keycloak
if (isset($_GET['code']) && !isset($_SESSION['user'])) {
    $token_url = "http://keycloak_auth:8080/auth/realms/$realm/protocol/openid-connect/token";
    
    $data = [
        'grant_type' => 'authorization_code',
        'code' => $_GET['code'],
        'redirect_uri' => $redirect_uri,
        'client_id' => $client_id,
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);
    
    if (isset($token_data['access_token'])) {
        // Décoder le token JWT pour obtenir les infos utilisateur
        $parts = explode('.', $token_data['access_token']);
        $payload = json_decode(base64_decode($parts[1]), true);
        $_SESSION['user'] = $payload['preferred_username'] ?? 'Utilisateur';
        $_SESSION['token'] = $token_data['access_token'];
    }
}

// Si pas connecté, rediriger vers Keycloak
if (!isset($_SESSION['user'])) {
    $auth_url = "$keycloak_url/realms/$realm/protocol/openid-connect/auth?" . http_build_query([
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'response_type' => 'code',
        'scope' => 'openid',
    ]);
    header("Location: $auth_url");
    exit;
}

$user = htmlspecialchars($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TP5 - Accueil</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        h1 { color: #333; }
        .menu { margin-top: 20px; }
        .menu a {
            display: inline-block; margin: 10px; padding: 15px 25px;
            background: #2196F3; color: white; text-decoration: none;
            border-radius: 5px; font-size: 16px;
        }
        .menu a:hover { background: #1976D2; }
        .logout { background: #f44336 !important; }
        .logout:hover { background: #d32f2f !important; }
        .user-info { color: #666; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>TP5 - Infrastructure Docker</h1>
    <p class="user-info">Connecté en tant que : <strong><?= $user ?></strong></p>
    
    <div class="menu">
        <a href="/master.php">Base Master</a>
        <a href="/slave.php">Base Slave</a>
        <a href="/mailpit.php">Mailpit</a>
        <a href="/index.php?logout=1" class="logout">Déconnexion</a>
    </div>
</body>
</html>
