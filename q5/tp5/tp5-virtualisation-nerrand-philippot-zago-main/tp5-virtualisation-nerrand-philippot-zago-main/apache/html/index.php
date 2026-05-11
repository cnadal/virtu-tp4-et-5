<?php echo "bonjour de MP";

#$provider = new Stevenmaguire\OAuth2\Client\Provider\Keycloak([
#        'authServerUrl'         => '{keycloak-server-url}',
#        'realm'                 => '{keycloak-realm}',
#        'clientId'              => '{keycloak-client-id}',
#        'clientSecret'          => '{keycloak-client-secret}',
#        'redirectUri'           => 'https://tp5.local/index.php',
#        'encryptionAlgorithm'   => 'RS256',                             // optional
#        'encryptionKeyPath'     => '../key.pem',                        // optional
#        'encryptionKey'         => 'contents_of_key_or_certificate',    // optional
#        'version'               => '20.0.1',                            // optional
#]);
#
#if (!isset($_GET['code'])) {
#
#    // If we don't have an authorization code then get one
#    $authUrl = $provider->getAuthorizationUrl();
#    $_SESSION['oauth2state'] = $provider->getState();
#    header('Location: '.$authUrl);
#    exit;
#
#// Check given state against previously stored one to mitigate CSRF attack
#} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
#
#    unset($_SESSION['oauth2state']);
#    exit('Invalid state, make sure HTTP sessions are enabled.');
#
#} else {
#
#    // Try to get an access token (using the authorization code grant)
#    try {
#        $token = $provider->getAccessToken('authorization_code', [
#                'code' => $_GET['code']
#        ]);
#
#        echo "<p><a href='listeVoitures.php'>Liste des voitures</a></p>";
#
#        echo "<p><a href='ajoutVoitureGarage.php'>Ajouter une voiture</a></p>";
#    } catch (Exception $e) {
#        exit('Failed to get access token: '.$e->getMessage());
#    }
#
#    // Optional: Now you have a token you can look up a users profile data
#    try {
#
#        // We got an access token, let's now get the user's details
#        $user = $provider->getResourceOwner($token);
#
#        // Use these details to create a new profile
#        printf('Hello %s!', $user->getName());
#
#    } catch (Exception $e) {
#        exit('Failed to get resource owner: '.$e->getMessage());
#    }
#
#    // Use this to interact with an API on the users behalf
#    echo $token->getToken();
#}
echo "<p><a href='listeVoitures.php'>Liste des voitures</a></p>";

echo "<p><a href='ajoutVoitureGarage.php'>Ajouter une voiture</a></p>";
