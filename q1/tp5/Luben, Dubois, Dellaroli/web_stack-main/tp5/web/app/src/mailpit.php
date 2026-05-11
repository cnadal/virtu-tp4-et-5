<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/auth.php';

ensureSessionStarted();

if (!isAuthenticated()) {
    header('Location: /?action=login');
    exit;
}

header('Location: /mailpit-ui/');
exit;
