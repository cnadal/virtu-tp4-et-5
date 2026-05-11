<?php
require_once __DIR__ . '/config.php';

$idToken = $_SESSION['id_token'] ?? null;

session_unset();
session_destroy();

$params = [
    'post_logout_redirect_uri' => APP_URL . '/index.php'
];

if ($idToken) {
    $params['id_token_hint'] = $idToken;
}

header('Location: ' . KC_LOGOUT_URL . '?' . http_build_query($params));
exit;