<?php
session_start();

// ====== PARAM�TRES KEYCLOAK ======
$keycloak_url = 'http://localhost:8080';
$keycloak_internal_url = 'http://keycloak_auth:8080';
$client_secret = 'VOTRE_CLIENT_SECRET';
$redirect_uri = 'http://localhost:80/index.php';

// Endpoints OIDC Keycloak
$realm = "";
$auth_endpoint = $keycloak_url . '/realms/' . $realm . '/protocol/openid-connect/auth';
$token_endpoint = $keycloak_internal_url . '/realms/' . $realm . '/protocol/openid-connect/token';
$logout_endpoint = $keycloak_url . '/realms/' . $realm . '/protocol/openid-connect/logout';

// ====== LOGOUT ======
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: ' . $logout_endpoint . '?client_id=' . $client_id . '&post_logout_redirect_uri=' . urlencode($redirect_uri));
    exit;
}

// ====== CALLBACK KEYCLOAK ======
if (isset($_GET['code'])) {
    $ch = curl_init($token_endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $_GET['code'],
        'redirect_uri' => $redirect_uri
    ]));

    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);

    if (isset($data['access_token'])) {
        $_SESSION['access_token'] = $data['access_token'];

        $token_parts = explode('.', $data['access_token']);
        if (count($token_parts) === 3) {
            $payload = json_decode(base64_decode($token_parts[1]), true);
            $_SESSION['user_info'] = $payload;
        }
        header('Location: /index.php');
        exit;
    } else {
        echo '<h3>Erreur Keycloak:</h3><pre>' . print_r($data, true) . '</pre>';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Authentification Keycloak</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .container { padding: 20px; border: 1px solid #ccc; max-width: 600px; }
        .btn { padding: 10px; background: #0066cc; color: white; text-decoration: none; }
        .btn-danger { background: #cc0000; }
        pre { background: #eee; padding: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Application PHP - Test Keycloak</h2>
    <?php if (isset($_SESSION['access_token'])): ?>
        <p><strong>Statut :</strong> ? Connect�</p>
        <p><strong>Utilisateur :</strong> <?= htmlspecialchars($_SESSION['user_info']['preferred_username'] ?? 'Inconnu') ?></p>
        <p><a href="?action=logout" class="btn btn-danger">Se d�connecter</a></p>
        <details>
            <summary>Voir JWT Payload</summary>
            <pre><?= print_r($_SESSION['user_info'], true) ?></pre>
        </details>
    <?php else: ?>
        <p><strong>Statut :</strong> ? Non connect�</p>
        <?php
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2state'] = $state;
        $login_url = $auth_endpoint . '?' . http_build_query([
                'client_id' => $client_id,
                'redirect_uri' => $redirect_uri,
                'response_type' => 'code',
                'scope' => 'openid profile email',
                'state' => $state
            ]);
        ?>
        <p><a href="<?= $login_url ?>" class="btn">Se connecter avec Keycloak</a></p>
    <?php endif; ?>
</div>
</body>
</html>
