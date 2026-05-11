<?php
session_start();

const APP_URL = 'http://tp5.local';

const KC_REALM = 'tp5';
const KC_CLIENT_ID = 'web_app';
const KC_CLIENT_SECRET = 'QBEUGgCZFWngmFtPYs9k9oeGrWKnlSbX';

const KC_AUTH_URL = 'http://localhost:8080/realms/' . KC_REALM . '/protocol/openid-connect/auth';
const KC_TOKEN_URL = 'http://keycloak_auth:8080/realms/' . KC_REALM . '/protocol/openid-connect/token';
const KC_USERINFO_URL = 'http://keycloak_auth:8080/realms/' . KC_REALM . '/protocol/openid-connect/userinfo';
const KC_LOGOUT_URL = 'http://localhost:8080/realms/' . KC_REALM . '/protocol/openid-connect/logout';

const KC_REDIRECT_URI = APP_URL . '/callback.php';

function postForm(string $url, array $data): array
{
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
            'ignore_errors' => true
        ]
    ];

    $result = file_get_contents($url, false, stream_context_create($options));
    if ($result === false) {
        return [];
    }

    $json = json_decode($result, true);
    return is_array($json) ? $json : [];
}

function getWithBearer(string $url, string $token): array
{
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer $token\r\n",
            'ignore_errors' => true
        ]
    ];

    $result = file_get_contents($url, false, stream_context_create($options));
    if ($result === false) {
        return [];
    }

    $json = json_decode($result, true);
    return is_array($json) ? $json : [];
}