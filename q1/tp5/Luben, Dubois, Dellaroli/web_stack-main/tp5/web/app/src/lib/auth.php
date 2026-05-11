<?php

declare(strict_types=1);

function authConfig(): array
{
	$appBaseUrl = rtrim((string) (getenv('APP_BASE_URL') ?: 'https://app.little.bloomyindev.me'), '/');
	$keycloakBaseUrl = rtrim((string) (getenv('KEYCLOAK_BASE_URL') ?: 'https://auth.little.bloomyindev.me'), '/');
	$realm = (string) (getenv('KEYCLOAK_REALM') ?: 'master');
	$clientId = (string) (getenv('KEYCLOAK_CLIENT_ID') ?: 'web-app');
	$clientSecret = (string) (getenv('KEYCLOAK_CLIENT_SECRET') ?: '');

	return [
		'app_base_url' => $appBaseUrl,
		'keycloak_base_url' => $keycloakBaseUrl,
		'realm' => $realm,
		'client_id' => $clientId,
		'client_secret' => $clientSecret,
		'redirect_uri' => $appBaseUrl . '/?action=callback',
		'authorization_endpoint' => sprintf('%s/realms/%s/protocol/openid-connect/auth', $keycloakBaseUrl, $realm),
		'token_endpoint' => sprintf('%s/realms/%s/protocol/openid-connect/token', $keycloakBaseUrl, $realm),
		'userinfo_endpoint' => sprintf('%s/realms/%s/protocol/openid-connect/userinfo', $keycloakBaseUrl, $realm),
		'logout_endpoint' => sprintf('%s/realms/%s/protocol/openid-connect/logout', $keycloakBaseUrl, $realm),
	];
}

function ensureSessionStarted(): void
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
}

function isAuthenticated(): bool
{
	ensureSessionStarted();
	return isset($_SESSION['auth_user']) && is_array($_SESSION['auth_user']);
}

function currentUser(): ?array
{
	ensureSessionStarted();
	if (!isAuthenticated()) {
		return null;
	}

	return $_SESSION['auth_user'];
}

function buildLoginUrl(): string
{
	ensureSessionStarted();
	$config = authConfig();

	$state = bin2hex(random_bytes(16));
	$_SESSION['oidc_state'] = $state;

	$params = [
		'client_id' => $config['client_id'],
		'redirect_uri' => $config['redirect_uri'],
		'response_type' => 'code',
		'scope' => 'openid profile email',
		'state' => $state,
	];

	return $config['authorization_endpoint'] . '?' . http_build_query($params);
}

function handleAuthCallback(string $code, string $state): void
{
	ensureSessionStarted();

	if (!isset($_SESSION['oidc_state']) || !is_string($_SESSION['oidc_state']) || !hash_equals($_SESSION['oidc_state'], $state)) {
		throw new RuntimeException('OIDC state invalide.');
	}

	unset($_SESSION['oidc_state']);

	$config = authConfig();
	$token = exchangeCodeForToken($config, $code);
	$userInfo = fetchUserInfo($config, $token['access_token']);

	$_SESSION['auth_user'] = [
		'username' => $userInfo['preferred_username'] ?? ($userInfo['sub'] ?? 'user'),
		'name' => $userInfo['name'] ?? ($userInfo['preferred_username'] ?? 'Utilisateur'),
		'email' => $userInfo['email'] ?? '',
		'access_token' => $token['access_token'],
		'refresh_token' => $token['refresh_token'] ?? null,
	];
}

function logoutUser(): string
{
	ensureSessionStarted();
	$config = authConfig();
	$postLogout = $config['app_base_url'];

	session_unset();
	session_destroy();

	return $config['logout_endpoint'] . '?' . http_build_query([
		'post_logout_redirect_uri' => $postLogout,
		'client_id' => $config['client_id'],
	]);
}

function exchangeCodeForToken(array $config, string $code): array
{
	if ($config['client_secret'] === '' || $config['client_secret'] === 'change_me') {
		throw new RuntimeException('Configuration invalide: KEYCLOAK_CLIENT_SECRET est vide ou vaut change_me.');
	}

	$postData = http_build_query([
		'grant_type' => 'authorization_code',
		'client_id' => $config['client_id'],
		'client_secret' => $config['client_secret'],
		'redirect_uri' => $config['redirect_uri'],
		'code' => $code,
	]);

	$ch = curl_init($config['token_endpoint']);
	curl_setopt_array($ch, [
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $postData,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
		CURLOPT_TIMEOUT => 10,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
	]);

	$response = curl_exec($ch);
	$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if ($response === false) {
		$error = curl_error($ch);
		curl_close($ch);
		throw new RuntimeException('Erreur cURL token endpoint: ' . $error);
	}

	curl_close($ch);

	if ($status < 200 || $status >= 300) {
		$details = '';
		$payload = json_decode($response, true);
		if (is_array($payload)) {
			$error = isset($payload['error']) ? (string) $payload['error'] : '';
			$description = isset($payload['error_description']) ? (string) $payload['error_description'] : '';
			if ($error !== '' || $description !== '') {
				$details = trim($error . ' ' . $description);
			}
		}

		if ($details === '') {
			$details = substr(trim($response), 0, 300);
		}

		throw new RuntimeException('Echec echange code/token, status HTTP: ' . $status . ($details !== '' ? ' - ' . $details : ''));
	}

	$payload = json_decode($response, true);
	if (!is_array($payload) || !isset($payload['access_token'])) {
		throw new RuntimeException('Reponse token endpoint invalide.');
	}

	return $payload;
}

function fetchUserInfo(array $config, string $accessToken): array
{
	$ch = curl_init($config['userinfo_endpoint']);
	curl_setopt_array($ch, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => [
			'Authorization: Bearer ' . $accessToken,
			'Accept: application/json',
		],
		CURLOPT_TIMEOUT => 10,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
	]);

	$response = curl_exec($ch);
	$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if ($response === false) {
		$error = curl_error($ch);
		curl_close($ch);
		throw new RuntimeException('Erreur cURL userinfo endpoint: ' . $error);
	}

	curl_close($ch);

	if ($status < 200 || $status >= 300) {
		throw new RuntimeException('Echec userinfo endpoint, status HTTP: ' . $status);
	}

	$payload = json_decode($response, true);
	if (!is_array($payload)) {
		throw new RuntimeException('Reponse userinfo invalide.');
	}

	return $payload;
}
