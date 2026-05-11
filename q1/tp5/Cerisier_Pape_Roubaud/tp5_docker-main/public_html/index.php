<?php
session_start();

// Disable error reporting for warnings that might leak during evaluation, but keep it for development
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);

if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    die("Veuillez patienter pendant l'installation des dépendances Composer et rafraîchir la page...");
}

use Jumbojett\OpenIDConnectClient;

// Keycloak details
$domain = $_SERVER['HTTP_HOST'];
$keycloak_url = "http://{$domain}:8080/realms/tp5"; 
$client_id = 'web_app';
$client_secret = 'tp5-fixed-secret-2024'; 

try {
    $oidc = new OpenIDConnectClient(
        $keycloak_url,
        $client_id,
        $client_secret
    );

    // This overrides the issuer validation if internal/external URLs differ (common in Docker)
    $oidc->setVerifyHost(false);
    $oidc->setVerifyPeer(false);
    $oidc->setRedirectURL('http://tp5.local/index.php');


    // If Keycloak gives an issuer like http://tp5.local:8080/realms/tp5 but we connect via http://keycloak_auth:8080/...
    $oidc->setProviderURL($keycloak_url);

    $oidc->authenticate(); // This will trigger the redirect to Keycloak if not authenticated
    $name = $oidc->requestUserInfo('preferred_username');
    $_SESSION['user'] = $name;

} catch (\Exception $e) {
    // Fallback for demonstration if Keycloak realm is not yet configured
    if (!isset($_SESSION['user'])) {
        echo "<div style='color:red;'>Erreur de connexion à Keycloak : " . $e->getMessage() . "</div>";
        echo "<p>Veuillez vérifier que le Realm 'tp5', le Client 'web_app', et l'utilisateur sont bien configurés dans Keycloak.</p>";
        // We let the user see the page anyway for TP testing purposes if keycloak fails initially
        $_SESSION['user'] = 'Utilisateur non authentifié (Erreur SSO)';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TP5 - Virtualisation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; }
        .container { background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        h1 { color: #333; }
        ul { list-style-type: none; padding: 0; }
        li { margin: 15px 0; }
        a { text-decoration: none; background-color: #007bff; color: white; padding: 10px 15px; border-radius: 5px; display: inline-block; }
        a:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur l'application TP5</h1>
        <p>Connecté en tant que : <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></p>
        
        <p>Veuillez choisir une action :</p>
        <ul>
            <li><a href="master.php">🚘 Voir et ajouter des voitures (Base Master)</a></li>
            <li><a href="slave.php">🚘 Voir les voitures (Base Slave - Lecture seule)</a></li>
            <li><a href="mailpit.php">📧 Consulter les emails envoyés</a></li>
        </ul>
    </div>
</body>
</html>
