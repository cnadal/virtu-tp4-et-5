<?php
/**
 * auth.php - Helper d'authentification Keycloak (OpenID Connect)
 * 
 * Ce fichier gère le flux OIDC :
 * 1. Vérifie si l'utilisateur a une session valide
 * 2. Sinon, redirige vers Keycloak pour l'authentification
 * 3. Échange le code d'autorisation contre un token
 */

session_start();

// Configuration Keycloak depuis les variables d'environnement
$keycloak_url = getenv('KEYCLOAK_URL') ?: 'http://keycloak_auth:8080';
$keycloak_public_url = getenv('KEYCLOAK_PUBLIC_URL') ?: 'http://tp5.local/auth';
$realm = getenv('KEYCLOAK_REALM') ?: 'tp5';
$client_id = getenv('KEYCLOAK_CLIENT_ID') ?: 'web_app';
$client_secret = getenv('KEYCLOAK_CLIENT_SECRET') ?: 'changeme_secret';

// URLs OpenID Connect
$auth_endpoint = "$keycloak_public_url/realms/$realm/protocol/openid-connect/auth";
$token_endpoint = "$keycloak_url/realms/$realm/protocol/openid-connect/token";
$userinfo_endpoint = "$keycloak_url/realms/$realm/protocol/openid-connect/userinfo";
$logout_endpoint = "$keycloak_public_url/realms/$realm/protocol/openid-connect/logout";

// URL de callback (retour après auth Keycloak)
$redirect_uri = "http://tp5.local/callback.php";

/**
 * Vérifie si l'utilisateur est authentifié
 */
function is_authenticated() {
    return isset($_SESSION['access_token']) && !empty($_SESSION['access_token']);
}

/**
 * Redirige vers Keycloak pour l'authentification
 */
function redirect_to_login() {
    global $auth_endpoint, $client_id, $redirect_uri;
    
    // Générer un state pour la sécurité CSRF
    $_SESSION['oauth_state'] = bin2hex(random_bytes(16));
    
    $params = http_build_query([
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'response_type' => 'code',
        'scope' => 'openid profile email',
        'state' => $_SESSION['oauth_state'],
    ]);
    
    header("Location: $auth_endpoint?$params");
    exit;
}

/**
 * Échange le code d'autorisation contre un token d'accès
 */
function exchange_code_for_token($code) {
    global $token_endpoint, $client_id, $client_secret, $redirect_uri;
    
    $data = [
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'code' => $code,
    ];
    
    $ch = curl_init($token_endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        return [
            'error' => true,
            'http_code' => $http_code,
            'response' => $response,
            'curl_error' => curl_error($ch)
        ];
    }
    
    return json_decode($response, true);
}

/**
 * Récupère les informations de l'utilisateur connecté
 */
function get_user_info() {
    global $userinfo_endpoint;
    
    if (!is_authenticated()) {
        return null;
    }
    
    $ch = curl_init($userinfo_endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['access_token'],
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Vérifie l'authentification et redirige si nécessaire
 */
function require_auth() {
    if (!is_authenticated()) {
        // Sauvegarder la page demandée pour redirection après auth
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
        redirect_to_login();
    }
}
