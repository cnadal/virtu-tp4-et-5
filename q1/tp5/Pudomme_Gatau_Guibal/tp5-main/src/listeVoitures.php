<?php
require_once 'auth.php';
require_once 'db.php';
require_auth();

$error = null;
$voitures = [];
$pdo = getDBConnection('slave'); // On lit depuis le slave par défaut (bonne pratique H.A.)

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM voiture ORDER BY id DESC");
        $voitures = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
} else {
    $error = "Connexion impossible à la base Slave.";
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter Voitures - TP5 Auto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        .car-id-box {
            background: linear-gradient(135deg, rgba(13,110,253,0.1) 0%, rgba(13,110,253,0.05) 100%);
            border: 1px solid rgba(13,110,253,0.2);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.2s;
        }
        .car-id-box:hover {
            border-color: rgba(13,110,253,0.5);
            transform: scale(1.02);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .car-id-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body style="background-color: #121212; color: #f8f9fa;">
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-list-ul text-primary me-2"></i>Liste des Voitures (Slave)</h2>
            <span class="badge bg-secondary">Lecture depuis mysql_slave</span>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
                <div class="mt-2 small text-secondary">La table voiture existe-t-elle ? Avez-vous configuré la réplication Master-Slave correctement ?</div>
            </div>
        <?php elseif (empty($voitures)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox text-secondary" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-secondary">Aucune voiture trouvée</h4>
                <p class="text-muted">Utilisez le menu "Ajouter Voiture" pour peupler la base de données.</p>
                <a href="/ajoutVoituresGarage.php" class="btn btn-outline-primary mt-2">Commencer l'ajout</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
                <?php foreach ($voitures as $v): ?>
                    <div class="col">
                        <div class="car-id-box">
                            <i class="bi bi-car-front text-secondary mb-2" style="font-size: 1.5rem;"></i>
                            <div class="car-id-number">#<?= htmlspecialchars($v['id']) ?></div>
                            <div class="small text-muted mt-1">ID Voiture</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
