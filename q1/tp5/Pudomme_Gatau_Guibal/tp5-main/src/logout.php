<?php
/**
 * logout.php - Déconnexion (supprime la session et redirige vers Keycloak logout)
 */

require_once 'auth.php';

$id_token = $_SESSION['id_token'] ?? '';

// Détruire la session PHP
session_destroy();

// Rediriger vers le logout Keycloak
$params = http_build_query([
    'post_logout_redirect_uri' => 'http://tp5.local/index.php',
    'id_token_hint' => $id_token,
    'client_id' => $client_id,
]);

header("Location: $logout_endpoint?$params");
exit;
