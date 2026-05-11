<?php
session_start();

// ─── Configuration Keycloak ───────────────────────────────────────────────────
$keycloak = [
    'auth_server_url' => 'http://keycloak_auth:8080',
    'realm'           => 'tp5',
    'client_id'       => 'tp5-app',
    'client_secret'   => 'TON_CLIENT_SECRET_ICI',
    'redirect_uri'    => 'http://tp5.local/auth.php',
];

$base = $keycloak['auth_server_url'] . '/realms/' . $keycloak['realm'] . '/protocol/openid-connect';

// ─── Étape 1 : pas encore de code → rediriger vers Keycloak ──────────────────
if (!isset($_GET['code'])) {
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth2state'] = $state;

    $params = http_build_query([
        'response_type' => 'code',
        'client_id'     => $keycloak['client_id'],
        'redirect_uri'  => $keycloak['redirect_uri'],
        'scope'         => 'openid profile email',
        'state'         => $state,
    ]);

    header('Location: ' . $base . '/auth?' . $params);
    exit;
}

// ─── Étape 2 : vérifier le state (protection CSRF) ───────────────────────────
if (empty($_GET['state']) || $_GET['state'] !== $_SESSION['oauth2state']) {
    unset($_SESSION['oauth2state']);
    http_response_code(400);
    exit('State invalide');
}
unset($_SESSION['oauth2state']);

// ─── Étape 3 : échanger le code contre un access token ───────────────────────
$postFields = http_build_query([
    'grant_type'    => 'authorization_code',
    'code'          => $_GET['code'],
    'redirect_uri'  => $keycloak['redirect_uri'],
    'client_id'     => $keycloak['client_id'],
    'client_secret' => $keycloak['client_secret'],
]);

$ch = curl_init($base . '/token');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $postFields,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
]);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    exit('Erreur cURL token : ' . curl_error($ch));
}
curl_close($ch);

$tokenData = json_decode($response, true);

if (empty($tokenData['access_token'])) {
    exit('Impossible d\'obtenir un access token : ' . htmlspecialchars($response));
}

// ─── Étape 4 : récupérer les infos de l'utilisateur (userinfo) ───────────────
$ch = curl_init($base . '/userinfo');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tokenData['access_token']],
]);
$userResponse = curl_exec($ch);

if (curl_errno($ch)) {
    exit('Erreur cURL userinfo : ' . curl_error($ch));
}
curl_close($ch);

$user = json_decode($userResponse, true);

if (empty($user['sub'])) {
    exit('Impossible de récupérer les infos utilisateur : ' . htmlspecialchars($userResponse));
}

$_SESSION['user'] = $user;

// ─── Rediriger vers la page principale ───────────────────────────────────────
header('Location: /index.php');
exit;