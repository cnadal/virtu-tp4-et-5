<?php
require_once 'auth.php';
require_once 'db.php';
require_auth();

$message = '';
$status = '';
$new_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection('master');
    
    if ($pdo) {
        try {
            // Insertion d'une ligne vide (seul l'ID auto_incrémenté sera généré)
            $sql = "INSERT INTO voiture () VALUES ()";
            $pdo->exec($sql);
            $new_id = $pdo->lastInsertId();
            
            $message = "Voiture ajoutée avec succès ! Nouvel ID : <strong>" . $new_id . "</strong> (dans la base Master)";
            $status = "success";
        } catch (PDOException $e) {
            $message = "Erreur lors de l'ajout : " . $e->getMessage() . "<br><small>Avez-vous créé la table avec l'outil précédent ?</small>";
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
    <title>Ajouter Voiture - TP5 Auto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        .id-badge {
            font-size: 3rem;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
    </style>
</head>
<body style="background-color: #121212; color: #f8f9fa;">
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-transparent border-secondary text-success">
                        <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Ajouter une Voiture</h4>
                    </div>
                    <div class="card-body text-center py-5">
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $status ?> text-start mb-4" role="alert">
                                <?= $message ?>
                            </div>
                            <?php if ($new_id): ?>
                                <div class="bg-dark border border-success rounded-circle text-success id-badge mb-4 shadow">
                                    #<?= $new_id ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <p class="text-secondary mb-4">La table voiture ne contient qu'un identifiant (ID).<br>Cliquez ci-dessous pour ajouter une nouvelle entrée générée automatiquement.</p>

                        <form method="POST" action="">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5">
                                <i class="bi bi-car-front me-2"></i> Générer une Nouvelle Voiture
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
