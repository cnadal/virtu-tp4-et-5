<?php
require_once 'auth.php';
require_once 'db.php';
require_auth();

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection('master');
    
    if ($pdo) {
        try {
            // Création de la table avec uniquement un id auto_incrémenté selon la consigne
            $sql = "CREATE TABLE IF NOT EXISTS voiture (
                id INT AUTO_INCREMENT PRIMARY KEY
            )";
            $pdo->exec($sql);
            $message = "La table 'voiture' a été créée ou vérifiée avec succès dans la base Master !";
            $status = "success";
        } catch (PDOException $e) {
            $message = "Erreur lors de la création : " . $e->getMessage();
            $status = "danger";
        }
    } else {
        $message = "Impossible de se connecter à la base Master.";
        $status = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer BDD - TP5 Auto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
</head>
<body style="background-color: #121212; color: #f8f9fa;">
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-transparent border-secondary text-primary">
                        <h4 class="mb-0"><i class="bi bi-database-add me-2"></i>Création de la Base de Données</h4>
                    </div>
                    <div class="card-body">
                        <p>Cette action va créer la table <code>voiture</code> avec un unique attribut <code>id</code> dans la base de données <strong>Master</strong>.</p>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $status ?> d-flex align-items-center" role="alert">
                                <i class="bi bi-<?= $status === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2 flex-shrink-0"></i>
                                <div><?= htmlspecialchars($message) ?></div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <i class="bi bi-play-fill me-1"></i> Exécuter le script
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
