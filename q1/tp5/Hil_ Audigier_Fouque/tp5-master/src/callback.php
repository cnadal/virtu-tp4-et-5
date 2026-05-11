<?php
require_once __DIR__ . '/config.php';

if (!isset($_GET['code'], $_GET['state'])) {
    exit('Erreur : code ou state manquant');
}

if (!isset($_SESSION['state']) || $_GET['state'] !== $_SESSION['state']) {
    exit('Erreur : state invalide');
}

$tokens = postForm(KC_TOKEN_URL, [
    'grant_type' => 'authorization_code',
    'client_id' => KC_CLIENT_ID,
    'client_secret' => KC_CLIENT_SECRET,
    'code' => $_GET['code'],
    'redirect_uri' => KC_REDIRECT_URI
]);

if (empty($tokens['access_token'])) {
    exit('Erreur : token non récupéré');
}

$user = getWithBearer(KC_USERINFO_URL, $tokens['access_token']);

$_SESSION['user'] = $user;
$_SESSION['access_token'] = $tokens['access_token'];
$_SESSION['id_token'] = $tokens['id_token'] ?? null;

header('Location: /index.php');
exit;