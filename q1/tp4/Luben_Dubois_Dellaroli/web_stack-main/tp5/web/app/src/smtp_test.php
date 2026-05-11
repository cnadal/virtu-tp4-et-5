<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/auth.php';

ensureSessionStarted();

if (!isAuthenticated()) {
    header('Location: /?action=login');
    exit;
}

$result = null;
$error = null;

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function smtpSend(
    string $host,
    int $port,
    string $from,
    string $to,
    string $subject,
    string $body
): void {
    $socket = @fsockopen($host, $port, $errno, $errstr, 5);

    if (!is_resource($socket)) {
        throw new RuntimeException('Connexion SMTP impossible: ' . $errstr . ' (' . $errno . ')');
    }

    stream_set_timeout($socket, 5);

    $readLine = static function ($stream): string {
        $line = fgets($stream);
        if ($line === false) {
            throw new RuntimeException('Reponse SMTP manquante.');
        }

        return trim($line);
    };

    $expectCode = static function ($line, array $codes): void {
        foreach ($codes as $code) {
            if (str_starts_with($line, (string) $code)) {
                return;
            }
        }

        throw new RuntimeException('Erreur SMTP: ' . $line);
    };

    $expectCode($readLine($socket), [220]);

    fwrite($socket, "EHLO web-app\r\n");
    while (true) {
        $line = $readLine($socket);
        if (preg_match('/^250[ -]/', $line) !== 1) {
            throw new RuntimeException('Erreur EHLO: ' . $line);
        }

        if (str_starts_with($line, '250 ')) {
            break;
        }
    }

    fwrite($socket, 'MAIL FROM:<' . $from . ">\r\n");
    $expectCode($readLine($socket), [250]);

    fwrite($socket, 'RCPT TO:<' . $to . ">\r\n");
    $expectCode($readLine($socket), [250, 251]);

    fwrite($socket, "DATA\r\n");
    $expectCode($readLine($socket), [354]);

    $headers = [
        'From: ' . $from,
        'To: ' . $to,
        'Subject: ' . $subject,
        'Date: ' . date(DATE_RFC2822),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
    ];

    $payload = implode("\r\n", $headers)
        . "\r\n\r\n"
        . str_replace("\n", "\r\n", $body)
        . "\r\n.\r\n";

    fwrite($socket, $payload);
    $expectCode($readLine($socket), [250]);

    fwrite($socket, "QUIT\r\n");
    fclose($socket);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = (string) (filter_input(INPUT_POST, 'host', FILTER_UNSAFE_RAW) ?: 'mailpit');
    $port = (int) (filter_input(INPUT_POST, 'port', FILTER_VALIDATE_INT) ?: 1025);
    $from = (string) (filter_input(INPUT_POST, 'from', FILTER_UNSAFE_RAW) ?: 'noreply@app.local');
    $to = (string) (filter_input(INPUT_POST, 'to', FILTER_UNSAFE_RAW) ?: 'student@example.local');
    $subject = (string) (filter_input(INPUT_POST, 'subject', FILTER_UNSAFE_RAW) ?: 'Test Mailpit');
    $body = (string) (filter_input(INPUT_POST, 'body', FILTER_UNSAFE_RAW) ?: 'Message de test depuis la page SMTP.');

    try {
        smtpSend($host, $port, $from, $to, $subject, $body);
        $result = 'Email envoye vers ' . $host . ':' . $port . '. Ouvrir Mailpit pour verifier la reception.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Test</title>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; background: #f6f7fb; color: #1f2937; margin: 0; }
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: #fff; border: 1px solid #d1d5db; border-radius: 12px; padding: 1rem; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        label { display: block; font-size: 0.9rem; margin-bottom: 0.3rem; }
        input, textarea, button { width: 100%; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: 8px; padding: 0.55rem; }
        textarea { min-height: 140px; }
        button { background: #0f766e; color: #fff; cursor: pointer; border: 0; margin-top: 0.75rem; }
        .success { color: #065f46; margin: 0.6rem 0; }
        .error { color: #991b1b; margin: 0.6rem 0; }
        .top-links { margin-bottom: 1rem; }
        .top-links a { color: #0f766e; margin-right: 1rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-links">
        <a href="/index.php">Retour menu</a>
        <a href="/mailpit.php">Ouvrir Mailpit</a>
    </div>

    <div class="card">
        <h1>Test SMTP vers Mailpit</h1>
        <p>Valeurs conseillees: host <strong>mailpit</strong>, port <strong>1025</strong>.</p>

        <?php if ($result !== null): ?>
            <p class="success"><?= h($result) ?></p>
        <?php endif; ?>

        <?php if ($error !== null): ?>
            <p class="error"><?= h($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <div class="grid">
                <div>
                    <label for="host">SMTP host</label>
                    <input id="host" name="host" value="<?= h((string) (filter_input(INPUT_POST, 'host', FILTER_UNSAFE_RAW) ?: 'mailpit')) ?>">
                </div>
                <div>
                    <label for="port">SMTP port</label>
                    <input id="port" name="port" type="number" value="<?= h((string) (filter_input(INPUT_POST, 'port', FILTER_UNSAFE_RAW) ?: '1025')) ?>">
                </div>
                <div>
                    <label for="from">From</label>
                    <input id="from" name="from" value="<?= h((string) (filter_input(INPUT_POST, 'from', FILTER_UNSAFE_RAW) ?: 'noreply@app.local')) ?>">
                </div>
                <div>
                    <label for="to">To</label>
                    <input id="to" name="to" value="<?= h((string) (filter_input(INPUT_POST, 'to', FILTER_UNSAFE_RAW) ?: 'student@example.local')) ?>">
                </div>
            </div>

            <div style="margin-top: 0.75rem;">
                <label for="subject">Sujet</label>
                <input id="subject" name="subject" value="<?= h((string) (filter_input(INPUT_POST, 'subject', FILTER_UNSAFE_RAW) ?: 'Test Mailpit')) ?>">
            </div>

            <div style="margin-top: 0.75rem;">
                <label for="body">Message</label>
                <textarea id="body" name="body"><?= h((string) (filter_input(INPUT_POST, 'body', FILTER_UNSAFE_RAW) ?: 'Message de test depuis la page SMTP.')) ?></textarea>
            </div>

            <button type="submit">Envoyer email de test</button>
        </form>
    </div>
</div>
</body>
</html>
