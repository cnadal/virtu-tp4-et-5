<?php
require_once 'auth.php';
require_auth();

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['to'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';
    
    // Validation basique
    if (empty($to) || empty($subject) || empty($body)) {
        $message = "Tous les champs sont requis.";
        $status = "warning";
    } elseif (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $message = "L'adresse email n'est pas valide.";
        $status = "warning";
    } else {
        // En-têtes pour un email HTML propre
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: App Voiture <app@tp5.local>" . "\r\n";
        
        // Formattage du contenu en HTML simple
        $htmlBody = "
        <html>
        <head>
            <title>$subject</title>
        </head>
        <body style='font-family: Arial, sans-serif; padding: 20px;'>
            <h2 style='color: #0d6efd;'>Système d'Information Garage</h2>
            <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #0d6efd;'>
                " . nl2br(htmlspecialchars($body)) . "
            </div>
            <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
            <small style='color: #888;'>Ce mail a été généré automatiquement par TP5 Auto et intercepté par Mailpit.</small>
        </body>
        </html>
        ";

        // Envoi via la fonction mail native (qui a été routée dans le Dockerfile via msmtp vers mailpit)
        if (mail($to, $subject, $htmlBody, $headers)) {
            $message = "Email envoyé avec succès ! Consultez Mailpit (mail.local) pour le lire.";
            $status = "success";
        } else {
            $message = "Erreur lors de l'envoi de l'email. Vérifiez la configuration (msmtp / sendmail_path).";
            $status = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoyer Mail - TP5 Auto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
</head>
<body style="background-color: #121212; color: #f8f9fa;">
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-transparent border-secondary text-info">
                        <h4 class="mb-0"><i class="bi bi-envelope-paper me-2"></i>Test Serveur SMTP</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $status ?> alert-dismissible fade show" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i> <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <p class="text-secondary pb-3 border-bottom border-secondary">
                            Utilisez ce formulaire pour tester l'envoi d'emails depuis PHP. Les emails ne seront pas réellement envoyés sur internet, mais interceptés par <a href="http://mail.local" target="_blank" class="text-info">Mailpit</a>.
                        </p>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="to" class="form-label text-light">Destinataire</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary border-secondary text-white"><i class="bi bi-person"></i></span>
                                    <input type="email" class="form-control bg-dark text-white border-secondary" id="to" name="to" value="client@example.com" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label text-light">Sujet</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary border-secondary text-white"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" id="subject" name="subject" value="Notification de votre garage" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="body" class="form-label text-light">Message</label>
                                <textarea class="form-control bg-dark text-white border-secondary" id="body" name="body" rows="5" required>Bonjour,
Nous confirmons la prise en compte de votre nouvelle voiture dans notre base de données.

Cordialement,
L'équipe TP5.</textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-info text-white">
                                    <i class="bi bi-send-fill me-2"></i> Envoyer le Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
