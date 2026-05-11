<?php
require_once __DIR__ . '/config.php';

$state = bin2hex(random_bytes(16));
$_SESSION['state'] = $state;

$params = [
    'client_id' => KC_CLIENT_ID,
    'redirect_uri' => KC_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'openid profile email',
    'state' => $state
];

header('Location: ' . KC_AUTH_URL . '?' . http_build_query($params));
exit;