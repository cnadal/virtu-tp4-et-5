<?php
/**
 * callback.php - Callback OpenID Connect
 * 
 * Keycloak redirige ici après l'authentification de l'utilisateur.
 * Ce script échange le code d'autorisation contre un token d'accès.
 */

require_once 'auth.php';

// Vérifier la présence du code d'autorisation
if (!isset($_GET['code'])) {
    header('Location: /index.php');
    exit;
}

if (!isset($_GET['state']) || $_GET['state'] !== ($_SESSION['oauth_state'] ?? '')) {
    $expected = $_SESSION['oauth_state'] ?? 'VIDE (Rejet Session)';
    $got = $_GET['state'] ?? 'VIDE';
    $host = $_SERVER['HTTP_HOST'] ?? 'Inconnu';
    die("<h3>Erreur de sécurité : state invalide.</h3>
         <p><strong>Reçu :</strong> $got</p>
         <p><strong>Attendu en session :</strong> $expected</p>
         <br>
         <p><strong>Diagnostic :</strong><br>
         Si 'Attendu' est VIDE, c'est que votre navigateur n'a pas transmis le cookie de session PHP.<br>
         Hostname actuel: <strong>$host</strong>.<br>
         Vérifiez bien que vous accédez à l'application via l'URL exacte <strong>http://tp5.local</strong> et jamais <em>localhost</em> ou <em>127.0.0.1</em>, car les cookies de session sont liés au nom de domaine exact !</p>");
}

// Échanger le code contre un token
$token_data = exchange_code_for_token($_GET['code']);

if ($token_data && !isset($token_data['error']) && isset($token_data['access_token'])) {
    $_SESSION['access_token'] = $token_data['access_token'];
    $_SESSION['refresh_token'] = $token_data['refresh_token'] ?? '';
    $_SESSION['id_token'] = $token_data['id_token'] ?? '';
    
    // Rediriger vers la page demandée initialement, ou l'accueil
    $return_to = $_SESSION['return_to'] ?? '/index.php';
    unset($_SESSION['return_to']);
    unset($_SESSION['oauth_state']);
    
    header("Location: $return_to");
    exit;
} else {
    $debug_info = htmlspecialchars(print_r($token_data, true));
    die("<h3>Erreur d'authentification : impossible d'obtenir le token.</h3>
         <p>Keycloak a refusé l'échange du code.</p>
         <pre style='background:#f4f4f4; padding:15px; border:1px solid #ccc; color:#d9534f; overflow-x:auto;'>$debug_info</pre>
         <p><a href='/'>Retour à l'accueil</a></p>");
}
