<?php
// ============================================================
//  ajoutVoituresGarage.php  –  Ajouter une voiture dans GARAGE
// ============================================================

$host     = 'db_mysql';   // Nom du service MySQL dans le réseau Docker
$user     = 'dbuser';     // MYSQL_USER dans var.env
$password = 'dbpassword'; // MYSQL_PASSWORD dans var.env

$message = '';
$erreur  = '';

// ── Connexion PDO ────────────────────────────────────────────
try {
    $pdo = new PDO("mysql:host=$host;dbname=GARAGE;charset=utf8mb4", $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("❌ Connexion impossible : " . $e->getMessage() .
            "<br>Assurez-vous d'avoir exécuté <a href='bd/creationBDD.php'>creationBDD.php</a> au préalable.");
}

// ── Traitement du formulaire (POST) ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération & nettoyage des données
    $marque      = trim($_POST['marque']      ?? '');
    $modele      = trim($_POST['modele']      ?? '');
    $annee       = (int)($_POST['annee']      ?? 0);
    $couleur     = trim($_POST['couleur']     ?? '');
    $carburant   = trim($_POST['carburant']   ?? '');
    $kilometrage = (int)($_POST['kilometrage']?? 0);
    $prix        = (float)($_POST['prix']     ?? 0);
    $disponible  = isset($_POST['disponible']) ? 1 : 0;

    $carburantsValides = ['Essence', 'Diesel', 'Hybride', 'Électrique', 'GPL'];

    // Validation simple
    if (empty($marque) || empty($modele) || empty($couleur)) {
        $erreur = "Marque, modèle et couleur sont obligatoires.";
    } elseif ($annee < 1900 || $annee > (int)date('Y') + 1) {
        $erreur = "Année invalide.";
    } elseif (!in_array($carburant, $carburantsValides)) {
        $erreur = "Type de carburant invalide.";
    } elseif ($prix <= 0) {
        $erreur = "Le prix doit être supérieur à 0.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO Voitures (marque, modele, annee, couleur, carburant, kilometrage, prix, disponible)
                VALUES (:marque, :modele, :annee, :couleur, :carburant, :kilometrage, :prix, :disponible)
            ");
            $stmt->execute([
                    ':marque'      => $marque,
                    ':modele'      => $modele,
                    ':annee'       => $annee,
                    ':couleur'     => $couleur,
                    ':carburant'   => $carburant,
                    ':kilometrage' => $kilometrage,
                    ':prix'        => $prix,
                    ':disponible'  => $disponible,
            ]);
            $message = "✅ La voiture <strong>$marque $modele ($annee)</strong> a été ajoutée avec succès ! (ID : " . $pdo->lastInsertId() . ")";
        } catch (PDOException $e) {
            $erreur = "❌ Erreur lors de l'insertion : " . $e->getMessage();
        }
    }
}

$anneeActuelle = (int)date('Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une voiture – Garage</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; color: #333; }
        header {
            background: #1a1a2e; color: #fff; padding: 18px 32px;
            display: flex; align-items: center; gap: 12px;
        }
        header h1 { font-size: 1.4rem; }
        nav { background: #16213e; padding: 10px 32px; }
        nav a { color: #aac4ff; text-decoration: none; margin-right: 20px; font-size: .9rem; }
        nav a:hover { color: #fff; }
        .container { max-width: 700px; margin: 40px auto; padding: 0 16px; }
        .card {
            background: #fff; border-radius: 10px;
            padding: 32px; box-shadow: 0 2px 12px rgba(0,0,0,.08);
        }
        .card h2 { margin-bottom: 24px; font-size: 1.2rem; color: #1a1a2e; }
        .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb;
            padding:12px 16px; border-radius:6px; margin-bottom:18px; }
        .alert-error   { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb;
            padding:12px 16px; border-radius:6px; margin-bottom:18px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }
        label { font-size: .85rem; font-weight: 600; color: #555; }
        input, select {
            padding: 9px 12px; border: 1px solid #ccc; border-radius: 6px;
            font-size: .95rem; transition: border-color .2s;
        }
        input:focus, select:focus { outline: none; border-color: #4a90e2; }
        .checkbox-group { flex-direction: row; align-items: center; gap: 10px; margin-top: 4px; }
        .checkbox-group input { width: auto; }
        button[type="submit"] {
            margin-top: 8px; width: 100%; padding: 12px;
            background: #1a1a2e; color: #fff; border: none;
            border-radius: 6px; font-size: 1rem; cursor: pointer;
            transition: background .2s;
        }
        button[type="submit"]:hover { background: #0f3460; }
    </style>
</head>
<body>

<header>
    <span style="font-size:1.8rem">🚗</span>
    <h1>Garage – Gestion des véhicules</h1>
</header>

<nav>
    <a href="listeVoitures.php">📋 Liste des voitures</a>
    <a href="ajoutVoituresGarage.php">➕ Ajouter une voiture</a>
    <a href="bd/creationBDD.php">🛠 Initialiser la BDD</a>
</nav>

<div class="container">
    <div class="card">
        <h2>➕ Ajouter une nouvelle voiture</h2>

        <?php if ($message): ?>
            <div class="alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($erreur): ?>
            <div class="alert-error"><?= $erreur ?></div>
        <?php endif; ?>

        <form method="POST" action="ajoutVoituresGarage.php">
            <div class="form-grid">

                <div class="form-group">
                    <label for="marque">Marque *</label>
                    <input type="text" id="marque" name="marque" placeholder="Ex : Renault"
                           value="<?= htmlspecialchars($_POST['marque'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="modele">Modèle *</label>
                    <input type="text" id="modele" name="modele" placeholder="Ex : Clio V"
                           value="<?= htmlspecialchars($_POST['modele'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="annee">Année *</label>
                    <input type="number" id="annee" name="annee"
                           min="1900" max="<?= $anneeActuelle + 1 ?>"
                           value="<?= htmlspecialchars($_POST['annee'] ?? $anneeActuelle) ?>" required>
                </div>

                <div class="form-group">
                    <label for="couleur">Couleur *</label>
                    <input type="text" id="couleur" name="couleur" placeholder="Ex : Rouge"
                           value="<?= htmlspecialchars($_POST['couleur'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="carburant">Carburant *</label>
                    <select id="carburant" name="carburant" required>
                        <?php foreach (['Essence','Diesel','Hybride','Électrique','GPL'] as $c): ?>
                            <option value="<?= $c ?>"
                                    <?= (($_POST['carburant'] ?? '') === $c) ? 'selected' : '' ?>>
                                <?= $c ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="kilometrage">Kilométrage (km)</label>
                    <input type="number" id="kilometrage" name="kilometrage" min="0"
                           value="<?= htmlspecialchars($_POST['kilometrage'] ?? '0') ?>">
                </div>

                <div class="form-group">
                    <label for="prix">Prix (€) *</label>
                    <input type="number" id="prix" name="prix" min="0" step="0.01"
                           value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>" required>
                </div>

                <div class="form-group" style="justify-content:flex-end; padding-bottom:4px">
                    <label>Disponibilité</label>
                    <div class="checkbox-group">
                        <input type="checkbox" id="disponible" name="disponible"
                                <?= isset($_POST['disponible']) || !isset($_POST['marque']) ? 'checked' : '' ?>>
                        <label for="disponible" style="font-weight:400">Véhicule disponible</label>
                    </div>
                </div>

            </div><!-- /.form-grid -->

            <button type="submit">Ajouter le véhicule</button>
        </form>
    </div>
</div>

</body>
</html>