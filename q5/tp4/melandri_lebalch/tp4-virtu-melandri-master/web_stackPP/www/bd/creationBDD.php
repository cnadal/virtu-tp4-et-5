<?php
// ============================================================
//  creationBDD.php  –  Création de la base GARAGE + table Voitures
//  La base n'est créée QUE si elle n'existe pas déjà.
// ============================================================

$host     = 'db_mysql';      // Nom du service MySQL dans le réseau Docker
$user     = 'root';          // root nécessaire pour CREATE DATABASE
$password = 'rootpassword';  // MYSQL_ROOT_PASSWORD dans var.env

try {
    // Connexion sans sélectionner de base (pour pouvoir créer la BDD)
    $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // ── 1. Création de la base de données (si elle n'existe pas) ──────────
    $pdo->exec("CREATE DATABASE IF NOT EXISTS GARAGE
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Base de données <strong>GARAGE</strong> prête (créée ou déjà existante).<br>";

    // ── 2. Sélection de la base ───────────────────────────────────────────
    $pdo->exec("USE GARAGE");

    // ── 3. Création de la table Voitures (si elle n'existe pas) ──────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS Voitures (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            marque      VARCHAR(50)     NOT NULL,
            modele      VARCHAR(80)     NOT NULL,
            annee       YEAR            NOT NULL,
            couleur     VARCHAR(30)     NOT NULL,
            carburant   ENUM('Essence','Diesel','Hybride','Électrique','GPL') NOT NULL,
            kilometrage INT UNSIGNED    NOT NULL DEFAULT 0,
            prix        DECIMAL(10, 2)  NOT NULL,
            disponible  TINYINT(1)      NOT NULL DEFAULT 1,
            created_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✅ Table <strong>Voitures</strong> prête.<br>";

    // ── 4. Insertion massive de données ──────────────────────────────────
    // On insère uniquement si la table est vide pour éviter les doublons
    $count = $pdo->query("SELECT COUNT(*) FROM Voitures")->fetchColumn();

    if ($count == 0) {
        $sql = "INSERT INTO Voitures (marque, modele, annee, couleur, carburant, kilometrage, prix, disponible)
                VALUES
                ('Renault',    'Clio V',            2022, 'Rouge',      'Essence',    12000,  14990.00, 1),
                ('Renault',    'Mégane IV',         2021, 'Bleu',       'Diesel',     35000,  17500.00, 1),
                ('Renault',    'Zoé',               2023, 'Blanc',      'Électrique',  8000,  22990.00, 1),
                ('Renault',    'Kadjar',            2020, 'Gris',       'Diesel',     52000,  18200.00, 0),
                ('Renault',    'Captur',            2022, 'Orange',     'Hybride',    15000,  21500.00, 1),
                ('Peugeot',    '208',               2023, 'Noir',       'Essence',     5000,  18900.00, 1),
                ('Peugeot',    '308 SW',            2021, 'Gris',       'Diesel',     40000,  19700.00, 1),
                ('Peugeot',    '3008',              2022, 'Blanc',      'Hybride',    22000,  31500.00, 0),
                ('Peugeot',    '508',               2020, 'Bleu Nuit',  'Diesel',     60000,  24000.00, 1),
                ('Peugeot',    'e-2008',            2023, 'Rouge',      'Électrique',  3000,  34990.00, 1),
                ('Citroën',    'C3',                2022, 'Jaune',      'Essence',    18000,  13900.00, 1),
                ('Citroën',    'C5 Aircross',       2021, 'Gris',       'Hybride',    30000,  27800.00, 1),
                ('Citroën',    'Berlingo',          2020, 'Blanc',      'Diesel',     75000,  16500.00, 0),
                ('Citroën',    'ë-C4',              2023, 'Vert',       'Électrique',  6000,  31990.00, 1),
                ('Citroën',    'C4 Cactus',         2019, 'Orange',     'Essence',    88000,  11200.00, 1),
                ('Volkswagen', 'Golf VIII',         2022, 'Noir',       'Essence',    25000,  25900.00, 1),
                ('Volkswagen', 'Passat',            2021, 'Argent',     'Diesel',     48000,  29500.00, 1),
                ('Volkswagen', 'Tiguan',            2023, 'Blanc',      'Essence',    10000,  35900.00, 0),
                ('Volkswagen', 'ID.4',              2022, 'Gris',       'Électrique', 18000,  42500.00, 1),
                ('Volkswagen', 'Polo',              2021, 'Rouge',      'Essence',    32000,  16800.00, 1),
                ('Toyota',     'Yaris',             2023, 'Bleu',       'Hybride',     4000,  21500.00, 1),
                ('Toyota',     'Corolla',           2022, 'Blanc',      'Hybride',    19000,  27900.00, 1),
                ('Toyota',     'RAV4',              2021, 'Gris',       'Hybride',    37000,  38500.00, 0),
                ('Toyota',     'C-HR',              2022, 'Noir',       'Hybride',    22000,  29900.00, 1),
                ('Toyota',     'Prius',             2020, 'Argent',     'Hybride',    55000,  24000.00, 1),
                ('Ford',       'Focus',             2021, 'Bleu',       'Essence',    40000,  17900.00, 1),
                ('Ford',       'Puma',              2022, 'Gris',       'Essence',    14000,  22500.00, 1),
                ('Ford',       'Kuga',              2021, 'Blanc',      'Hybride',    28000,  30900.00, 0),
                ('Ford',       'Mustang Mach-E',    2023, 'Rouge',      'Électrique',  7000,  49900.00, 1),
                ('Ford',       'Fiesta',            2020, 'Orange',     'Essence',    50000,  13500.00, 1),
                ('BMW',        'Série 3',           2022, 'Noir',       'Diesel',     30000,  42900.00, 1),
                ('BMW',        'X3',                2021, 'Blanc',      'Diesel',     45000,  48500.00, 1),
                ('BMW',        'iX3',               2023, 'Gris',       'Électrique',  9000,  62900.00, 0),
                ('BMW',        'Série 1',           2022, 'Bleu',       'Essence',    20000,  34900.00, 1),
                ('BMW',        '330e',              2021, 'Argent',     'Hybride',    35000,  47000.00, 1),
                ('Mercedes',   'Classe A',          2022, 'Blanc',      'Essence',    17000,  36500.00, 1),
                ('Mercedes',   'Classe C',          2021, 'Gris',       'Diesel',     38000,  44900.00, 1),
                ('Mercedes',   'GLC',               2023, 'Noir',       'Hybride',    12000,  58900.00, 0),
                ('Mercedes',   'EQA',               2022, 'Blanc',      'Électrique', 14000,  52900.00, 1),
                ('Mercedes',   'Classe B',          2020, 'Rouge',      'Diesel',     60000,  26500.00, 1),
                ('Audi',       'A3 Sportback',      2022, 'Gris',       'Essence',    22000,  35900.00, 1),
                ('Audi',       'Q3',                2021, 'Blanc',      'Essence',    36000,  41500.00, 1),
                ('Audi',       'e-tron',            2022, 'Noir',       'Électrique', 25000,  68900.00, 0),
                ('Audi',       'A4 Avant',          2020, 'Bleu',       'Diesel',     55000,  32000.00, 1),
                ('Audi',       'Q5',                2023, 'Gris',       'Hybride',     8000,  59900.00, 1),
                ('Opel',       'Corsa',             2022, 'Rouge',      'Essence',    16000,  16500.00, 1),
                ('Opel',       'Astra',             2021, 'Gris',       'Diesel',     42000,  19900.00, 1),
                ('Opel',       'Mokka-e',           2023, 'Blanc',      'Électrique',  5000,  34900.00, 1),
                ('Opel',       'Grandland',         2022, 'Bleu',       'Hybride',    20000,  31500.00, 0),
                ('Seat',       'Ibiza',             2022, 'Jaune',      'Essence',    14000,  15900.00, 1),
                ('Seat',       'Leon',              2021, 'Noir',       'Essence',    28000,  22900.00, 1),
                ('Seat',       'Ateca',             2020, 'Gris',       'Diesel',     50000,  24500.00, 1),
                ('Hyundai',    'i30',               2022, 'Blanc',      'Essence',    19000,  21900.00, 1),
                ('Hyundai',    'Tucson',            2023, 'Gris',       'Hybride',     7000,  33900.00, 0),
                ('Hyundai',    'IONIQ 5',           2022, 'Vert',       'Électrique', 16000,  44900.00, 1),
                ('Kia',        'Sportage',          2022, 'Blanc',      'Hybride',    21000,  31900.00, 1),
                ('Kia',        'Niro EV',           2023, 'Gris',       'Électrique',  4000,  38900.00, 1),
                ('Kia',        'Ceed',              2021, 'Rouge',      'Essence',    33000,  19500.00, 0),
                ('Fiat',       '500e',              2022, 'Bleu',       'Électrique', 11000,  27900.00, 1),
                ('Fiat',       'Tipo',              2020, 'Gris',       'Diesel',     65000,  13900.00, 1),
                ('Dacia',      'Sandero',           2022, 'Gris',       'Essence',    23000,  10990.00, 1),
                ('Dacia',      'Duster',            2021, 'Vert',       'GPL',        38000,  16500.00, 1),
                ('Dacia',      'Spring',            2023, 'Orange',     'Électrique',  6000,  16990.00, 0),
                ('Nissan',     'Juke',              2022, 'Jaune',      'Essence',    18000,  22900.00, 1),
                ('Nissan',     'Leaf',              2021, 'Blanc',      'Électrique', 30000,  24500.00, 1),
                ('Tesla',      'Model 3',           2022, 'Blanc',      'Électrique', 27000,  44990.00, 1),
                ('Tesla',      'Model Y',           2023, 'Noir',       'Électrique',  9000,  54990.00, 0),
                ('Volvo',      'XC40 Recharge',     2022, 'Bleu',       'Électrique', 20000,  52900.00, 1),
                ('Volvo',      'V60',               2021, 'Argent',     'Hybride',    34000,  39900.00, 1),
                ('Skoda',      'Octavia',           2021, 'Gris',       'Diesel',     44000,  23900.00, 1),
                ('Skoda',      'Kamiq',             2022, 'Blanc',      'Essence',    17000,  24500.00, 1)
        ";

        $pdo->exec($sql);
        $inserted = $pdo->query("SELECT COUNT(*) FROM Voitures")->fetchColumn();
        echo "✅ <strong>$inserted voitures</strong> insérées avec succès.<br>";
    } else {
        echo "ℹ️ La table contient déjà <strong>$count enregistrement(s)</strong>, insertion ignorée.<br>";
    }

    echo "<br>🏁 Initialisation terminée.";

} catch (PDOException $e) {
    die("❌ Erreur PDO : " . $e->getMessage());
}