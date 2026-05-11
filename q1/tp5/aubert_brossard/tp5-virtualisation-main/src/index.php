<?php
session_start();

// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$error_message = '';

// Traitement du formulaire de connexion
if (!isset($_SESSION['authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Envoi de la requête à l'API Token de Keycloak via cURL (Direct Access Grants)
        $url = 'http://keycloak_auth:8080/realms/tp5_realm/protocol/openid-connect/token';
        
        $data = [
            'client_id' => 'php_web_app',
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Comme on communique de conteneur à conteneur via le réseau interne Docker, l'URL est http://keycloak_auth
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            $token_data = json_decode($response, true);
            $_SESSION['authenticated'] = true;
            $_SESSION['access_token'] = $token_data['access_token'];
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        } else {
            $error_data = json_decode($response, true);
            $error_message = isset($error_data['error_description']) 
                ? "Erreur Keycloak: " . $error_data['error_description'] 
                : "Identifiants invalides.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TP5 - Accueil (Keycloak)</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; background-color: #f5f5f5; }
        .container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 400px; }
        .error { color: #d32f2f; background-color: #ffebee; padding: 10px; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #ffcdd2;}
        .login-form div { margin-bottom: 1rem; }
        .login-form label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .login-form input { width: 100%; padding: 0.5rem; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .login-form button { width: 100%; padding: 0.75rem; background-color: #1976d2; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem;}
        .login-form button:hover { background-color: #1565c0; }
        ul { list-style: none; padding: 0; }
        ul li { margin-bottom: 0.5rem; }
        ul li a { text-decoration: none; color: #1976d2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>TP5 Virtualisation</h1>
        
        <?php if (!isset($_SESSION['authenticated'])): ?>
            <p>Veuillez entrer vos identifiants pour vous connecter.</p>
            
            <?php if ($error_message): ?>
                <div class="error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div>
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" placeholder="ex: test" required>
                </div>
                <div>
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" placeholder="ex: testpwd" required>
                </div>
                <button type="submit">Se connecter avec Keycloak</button>
            </form>
            <p style="font-size: 0.8rem; color: #666; margin-top: 2rem;">Authentification vérifiée par <strong>Keycloak</strong> via API REST.</p>
        <?php else: ?>
            <p style="color: green;">Connecté avec succès en tant que <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Utilisateur') ?></strong> !</p>
            <h3>Services disponibles :</h3>
            <ul>
                <li><a href="master.php">➔ Accéder à la base Master</a></li>
                <li><a href="slave.php">➔ Accéder à la base Slave</a></li>
                <li><a href="mailpit.php">➔ Voir la messagerie Mailpit</a></li>
            </ul>
            <br>
            <ul>
                <li><a href="?logout=1" style="color: #d32f2f;">Se déconnecter</a></li>
            </ul>

        <?php endif; ?>
    </div>
</body>
</html>
