<?php
/**
 * auth.php — Helper OIDC Keycloak
 * Inclure en début de chaque page protégée.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// URL externe (navigateur du client)
define('KC_EXT_URL',  'http://tp5.local');
// URL interne (appel serveur-à-serveur depuis web_app)
define('KC_INT_URL',  'http://keycloak_auth:8080');
define('KC_REALM',    'tp5');
define('KC_CLIENT',   'tp5-app');
define('APP_BASE',    'http://tp5.local');

/**
 * Construit l'URL de login Keycloak pour la page courante.
 */
function kcLoginUrl(string $page): string
{
    return KC_EXT_URL . '/realms/' . KC_REALM . '/protocol/openid-connect/auth?' . http_build_query([
        'client_id'     => KC_CLIENT,
        'redirect_uri'  => APP_BASE . '/' . $page,
        'response_type' => 'code',
        'scope'         => 'openid profile email',
    ]);
}

/**
 * Échange le code d'autorisation contre un access_token.
 * L'appel se fait en interne (keycloak_auth:8080).
 */
function kcExchangeCode(string $code, string $page): ?array
{
    $url  = KC_INT_URL . '/realms/' . KC_REALM . '/protocol/openid-connect/token';
    $body = http_build_query([
        'grant_type'   => 'authorization_code',
        'client_id'    => KC_CLIENT,
        'redirect_uri' => APP_BASE . '/' . $page,
        'code'         => $code,
    ]);

    $ctx = stream_context_create([
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content'       => $body,
            'ignore_errors' => true,
        ],
    ]);

    $res = @file_get_contents($url, false, $ctx);
    return $res ? json_decode($res, true) : null;
}

/**
 * Décode la partie payload d'un JWT (sans vérification de signature).
 */
function kcDecodePayload(string $token): array
{
    $parts = explode('.', $token);
    if (count($parts) < 2) return [];
    $pad = strlen($parts[1]) % 4;
    $b64 = $pad ? $parts[1] . str_repeat('=', 4 - $pad) : $parts[1];
    return json_decode(base64_decode($b64), true) ?? [];
}

/**
 * Vérifie que l'utilisateur est authentifié.
 * Si non, gère le callback OAuth ou redirige vers Keycloak.
 *
 * @param string $page  Nom du fichier PHP courant (ex: 'master.php')
 */
function requireAuth(string $page = 'index.php'): void
{
    // 1) Callback Keycloak : échange du code
    if (!isset($_SESSION['kc_user']) && isset($_GET['code'])) {
        $tokens = kcExchangeCode($_GET['code'], $page);
        if ($tokens && isset($tokens['access_token'])) {
            $payload = kcDecodePayload($tokens['access_token']);
            $_SESSION['kc_user']  = $payload['preferred_username'] ?? 'utilisateur';
            $_SESSION['kc_email'] = $payload['email'] ?? '';
            $_SESSION['kc_token'] = $tokens['access_token'];
            // Redirection propre pour supprimer ?code= de l'URL
            header('Location: /' . $page);
            exit;
        }
    }

    // 2) Pas de session → redirection vers Keycloak
    if (!isset($_SESSION['kc_user'])) {
        header('Location: ' . kcLoginUrl($page));
        exit;
    }
}

/**
 * Déconnexion : détruit la session et redirige.
 */
function requireLogout(): void
{
    session_destroy();
    $logoutUrl = KC_EXT_URL . '/realms/' . KC_REALM . '/protocol/openid-connect/logout?' . http_build_query([
        'post_logout_redirect_uri' => APP_BASE . '/index.php',
        'client_id'                => KC_CLIENT,
    ]);
    header('Location: ' . $logoutUrl);
    exit;
}
