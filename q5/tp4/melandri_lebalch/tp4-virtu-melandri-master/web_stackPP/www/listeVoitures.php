<?php
// ============================================================
//  listeVoitures.php  –  Afficher la liste des voitures du garage
// ============================================================

$host     = 'db_mysql';   // Nom du service MySQL dans le réseau Docker
$user     = 'dbuser';     // MYSQL_USER dans var.env
$password = 'dbpassword'; // MYSQL_PASSWORD dans var.env

try {
    $pdo = new PDO("mysql:host=$host;dbname=GARAGE;charset=utf8mb4", $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("❌ Connexion impossible : " . $e->getMessage() .
            "<br>Assurez-vous d'avoir exécuté <a href='bd/creationBDD.php'>creationBDD.php</a> au préalable.");
}

// ── Filtres GET ──────────────────────────────────────────────
$filtreMarque    = trim($_GET['marque']    ?? '');
$filtreCarburant = trim($_GET['carburant'] ?? '');
$filtreDisponible= $_GET['disponible'] ?? '';
$filtreRecherche = trim($_GET['recherche'] ?? '');

// Construction de la requête avec filtres dynamiques
$conditions = [];
$params     = [];

if ($filtreMarque !== '') {
    $conditions[] = "marque = :marque";
    $params[':marque'] = $filtreMarque;
}
if ($filtreCarburant !== '') {
    $conditions[] = "carburant = :carburant";
    $params[':carburant'] = $filtreCarburant;
}
if ($filtreDisponible !== '') {
    $conditions[] = "disponible = :disponible";
    $params[':disponible'] = (int)$filtreDisponible;
}
if ($filtreRecherche !== '') {
    $conditions[] = "(marque LIKE :recherche OR modele LIKE :recherche OR couleur LIKE :recherche)";
    $params[':recherche'] = "%$filtreRecherche%";
}

$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";
$sql   = "SELECT * FROM Voitures $where ORDER BY marque, modele";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$voitures = $stmt->fetchAll();

// ── Récupération des marques distinctes (pour le filtre) ────
$marques    = $pdo->query("SELECT DISTINCT marque FROM Voitures ORDER BY marque")->fetchAll(PDO::FETCH_COLUMN);
$total      = $pdo->query("SELECT COUNT(*) FROM Voitures")->fetchColumn();
$totalFiltre= count($voitures);

// ── Couleurs carburant ───────────────────────────────────────
$badgeCarburant = [
        'Essence'     => '#e67e22',
        'Diesel'      => '#2980b9',
        'Hybride'     => '#27ae60',
        'Électrique'  => '#8e44ad',
        'GPL'         => '#c0392b',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des voitures – Garage</title>
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
        .container { max-width: 1200px; margin: 30px auto; padding: 0 16px; }

        /* Barre de filtres */
        .filters {
            background: #fff; border-radius: 10px; padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,.07); margin-bottom: 22px;
            display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;
        }
        .filters .f-group { display:flex; flex-direction:column; gap:5px; }
        .filters label { font-size:.8rem; font-weight:600; color:#555; }
        .filters input, .filters select {
            padding: 7px 10px; border: 1px solid #ccc; border-radius: 6px; font-size:.9rem;
        }
        .filters button {
            padding: 7px 18px; background:#1a1a2e; color:#fff;
            border:none; border-radius:6px; cursor:pointer; font-size:.9rem;
        }
        .filters button.reset { background:#6c757d; }
        .filters button:hover { opacity:.85; }

        /* Stats */
        .stats { margin-bottom: 14px; font-size: .9rem; color: #555; }
        .stats strong { color: #1a1a2e; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table {
            width: 100%; border-collapse: collapse;
            background: #fff; border-radius: 10px; overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,.07);
        }
        thead { background: #1a1a2e; color: #fff; }
        thead th { padding: 12px 14px; text-align: left; font-size: .85rem; white-space:nowrap; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tbody tr:hover { background: #eef2ff; }
        td { padding: 10px 14px; font-size: .88rem; vertical-align: middle; }
        .badge {
            display: inline-block; padding: 3px 9px; border-radius: 12px;
            color: #fff; font-size: .78rem; font-weight: 600;
        }
        .dispo-oui { color: #27ae60; font-weight:700; }
        .dispo-non { color: #e74c3c; font-weight:700; }
        .no-result {
            background:#fff; border-radius:10px; padding:40px;
            text-align:center; color:#888;
            box-shadow: 0 2px 8px rgba(0,0,0,.07);
        }
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

    <!-- ── Filtres ─────────────────────────────────────────── -->
    <form method="GET" action="listeVoitures.php">
        <div class="filters">

            <div class="f-group">
                <label for="f-recherche">🔍 Recherche libre</label>
                <input type="text" id="f-recherche" name="recherche"
                       placeholder="Marque, modèle, couleur…"
                       value="<?= htmlspecialchars($filtreRecherche) ?>">
            </div>

            <div class="f-group">
                <label for="f-marque">Marque</label>
                <select id="f-marque" name="marque">
                    <option value="">Toutes</option>
                    <?php foreach ($marques as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>"
                                <?= $filtreMarque === $m ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="f-group">
                <label for="f-carburant">Carburant</label>
                <select id="f-carburant" name="carburant">
                    <option value="">Tous</option>
                    <?php foreach (['Essence','Diesel','Hybride','Électrique','GPL'] as $c): ?>
                        <option value="<?= $c ?>" <?= $filtreCarburant === $c ? 'selected' : '' ?>>
                            <?= $c ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="f-group">
                <label for="f-dispo">Disponibilité</label>
                <select id="f-dispo" name="disponible">
                    <option value="">Toutes</option>
                    <option value="1" <?= $filtreDisponible === '1' ? 'selected' : '' ?>>Disponible</option>
                    <option value="0" <?= $filtreDisponible === '0' ? 'selected' : '' ?>>Indisponible</option>
                </select>
            </div>

            <div class="f-group" style="flex-direction:row;gap:8px;align-self:flex-end">
                <button type="submit">Filtrer</button>
                <a href="listeVoitures.php"><button type="button" class="reset">Réinitialiser</button></a>
            </div>
        </div>
    </form>

    <!-- ── Stats ───────────────────────────────────────────── -->
    <p class="stats">
        Affichage de <strong><?= $totalFiltre ?></strong> voiture(s)
        <?= ($totalFiltre < $total) ? "sur <strong>$total</strong> au total" : "au total" ?>
    </p>

    <!-- ── Tableau ─────────────────────────────────────────── -->
    <?php if (empty($voitures)): ?>
        <div class="no-result">
            <p style="font-size:2rem">🔎</p>
            <p>Aucune voiture ne correspond à vos critères.</p>
            <a href="listeVoitures.php" style="color:#4a90e2">Voir toutes les voitures</a>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>Année</th>
                    <th>Couleur</th>
                    <th>Carburant</th>
                    <th>Kilométrage</th>
                    <th>Prix</th>
                    <th>Disponible</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($voitures as $v): ?>
                    <tr>
                        <td><?= $v['id'] ?></td>
                        <td><strong><?= htmlspecialchars($v['marque']) ?></strong></td>
                        <td><?= htmlspecialchars($v['modele']) ?></td>
                        <td><?= $v['annee'] ?></td>
                        <td><?= htmlspecialchars($v['couleur']) ?></td>
                        <td>
                            <span class="badge"
                                  style="background:<?= $badgeCarburant[$v['carburant']] ?? '#555' ?>">
                                <?= htmlspecialchars($v['carburant']) ?>
                            </span>
                        </td>
                        <td><?= number_format($v['kilometrage'], 0, ',', ' ') ?> km</td>
                        <td><strong><?= number_format($v['prix'], 2, ',', ' ') ?> €</strong></td>
                        <td>
                            <?php if ($v['disponible']): ?>
                                <span class="dispo-oui">✔ Oui</span>
                            <?php else: ?>
                                <span class="dispo-non">✘ Non</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>
</body>
</html>