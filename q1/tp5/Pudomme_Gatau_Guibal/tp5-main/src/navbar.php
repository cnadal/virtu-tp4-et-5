<?php
/**
 * navbar.php - Composant de navigation commun
 */
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/index.php">
            <i class="bi bi-car-front-fill me-2"></i>TP5 Auto
        </a>
        <button class="navbar-toggler" type="submit" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>" href="/index.php">
                        <i class="bi bi-house-door me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'creationBDD.php' ? 'active' : '' ?>" href="/creationBDD.php">
                        <i class="bi bi-database-add me-1"></i>Créer BDD
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'ajoutVoituresGarage.php' ? 'active' : '' ?>" href="/ajoutVoituresGarage.php">
                        <i class="bi bi-plus-circle me-1"></i>Ajouter Voiture
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'listeVoitures.php' ? 'active' : '' ?>" href="/listeVoitures.php">
                        <i class="bi bi-list-ul me-1"></i>Consulter Voitures
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'envoiMail.php' ? 'active' : '' ?>" href="/envoiMail.php">
                        <i class="bi bi-envelope me-1"></i>Envoyer Mail
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" href="http://mail.local" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Ouvrir Mailpit
                    </a>
                </li>
            </ul>
            <div class="d-flex">
                <a href="/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                </a>
            </div>
        </div>
    </div>
</nav>
