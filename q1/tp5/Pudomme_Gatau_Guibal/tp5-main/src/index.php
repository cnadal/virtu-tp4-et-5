<?php
/**
 * index.php - Page d'accueil (authentifiée)
 */
require_once 'auth.php';
require_auth();

$user = get_user_info();
$username = $user['preferred_username'] ?? $user['name'] ?? 'Utilisateur';
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TP5 - Interface Garage</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #121212; color: #f8f9fa; }
        .hero {
            padding: 80px 0;
            background: linear-gradient(135deg, rgba(13,110,253,0.1) 0%, rgba(13,202,240,0.1) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .feature-card {
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 24px;
            height: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            border-color: #0d6efd;
            color: inherit;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">
                <i class="bi bi-car-front-fill text-primary me-2"></i>Gestion Garage
            </h1>
            <p class="lead text-secondary mb-4">
                Bienvenue <strong><?= htmlspecialchars($username) ?></strong> !<br>
                Gérez la base de données de vos voitures et communiquez facilement via SMTP.
            </p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <a href="/creationBDD.php" class="feature-card">
                    <div class="feature-icon"><i class="bi bi-database-add"></i></div>
                    <h4>1. Initialisation</h4>
                    <p class="text-secondary mb-0">Créer la table 'voiture' dans la base de données master.</p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="/ajoutVoituresGarage.php" class="feature-card">
                    <div class="feature-icon"><i class="bi bi-plus-circle"></i></div>
                    <h4>2. Ajout</h4>
                    <p class="text-secondary mb-0">Ajouter une nouvelle voiture (id auto-incrémenté).</p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="/listeVoitures.php" class="feature-card">
                    <div class="feature-icon"><i class="bi bi-view-list"></i></div>
                    <h4>3. Consultation</h4>
                    <p class="text-secondary mb-0">Visualiser les voitures depuis la base slave (réplication).</p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="/envoiMail.php" class="feature-card">
                    <div class="feature-icon"><i class="bi bi-envelope-paper"></i></div>
                    <h4>4. Communication</h4>
                    <p class="text-secondary mb-0">Envoyer un mail de test intercepté par Mailpit.</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
