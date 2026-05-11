<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}