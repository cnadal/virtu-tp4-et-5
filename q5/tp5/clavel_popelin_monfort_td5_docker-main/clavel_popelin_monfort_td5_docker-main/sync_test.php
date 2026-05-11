<?php
$master_host = 'mysql_master';
$slave_host  = 'mysql_slave';
$db   = 'db';
$user_master = 'root';
$user_slave  = 'user';
$pass = 'password';
$port = 3306;
$charset = 'utf8mb4';

// Initialisation sécurisée des variables pour éviter les "Undefined variable"
$master_rows = [];
$slave_rows  = [];
$message = '';
$error   = '';

$opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

function connectDB($host, $db, $user, $pass, $port, $charset, $opts) {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    return new PDO($dsn, $user, $pass, $opts);
}

try {
    // Tentative de connexion aux deux instances
    $pdo_master = connectDB($master_host, $db, $user_master, $pass, $port, $charset, $opts);
    $pdo_slave  = connectDB($slave_host,  $db, $user_slave, $pass, $port, $charset, $opts);

    // --- ACTION : SUPPRESSION SUR LE MASTER ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
        $id = (int)$_POST['delete_id'];
        $stmt = $pdo_master->prepare("DELETE FROM voitures WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Voiture #$id supprimée du Master. La réplication est en cours...";

        // Pause pour laisser le temps au Slave de recevoir l'info
        usleep(500000);
    }

    // --- LECTURE MASTER ---
    try {
        $master_rows = $pdo_master->query("SELECT * FROM voitures ORDER BY id ASC")->fetchAll();
    } catch (PDOException $e) {
        $error .= "Erreur Master : " . $e->getMessage() . "<br>";
    }

    // --- LECTURE SLAVE ---
    try {
        $slave_rows = $pdo_slave->query("SELECT * FROM voitures ORDER BY id ASC")->fetchAll();
    } catch (PDOException $e) {
        // C'est ici que l'erreur 1146 est capturée si la table n'est pas encore répliquée
        $slave_rows = [];
        $error .= "Le Slave n'a pas encore répliqué la table : " . $e->getMessage();
    }

} catch (PDOException $e) {
    $error = "Erreur de connexion globale : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test de synchronisation MySQL</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f7f6; }
        .container { display: flex; gap: 20px; }
        .card { flex: 1; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; }
        .master-h { border-top: 5px solid #2c3e50; }
        .slave-h { border-top: 5px solid #27ae60; }
        .msg { background: #d4efdf; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .err { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        button { background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
        button:hover { background: #c0392b; }
    </style>
</head>
<body>
<h1>🛠 Contrôle de Réplication Master-Slave</h1>

<?php if ($message): ?><div class="msg"><?= $message ?></div><?php endif; ?>
<?php if ($error):   ?><div class="err"><?= $error ?></div><?php endif; ?>

<div class="container">
    <div class="card master-h">
        <h2>🖥 Master (Écritures)</h2>
        <table>
            <tr><th>ID</th><th>Immatriculation</th><th>Action</th></tr>
            <?php foreach ($master_rows as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><strong><?= htmlspecialchars($row['immatriculation']) ?></strong></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card slave-h">
        <h2>💾 Slave (Lecture seule)</h2>
        <table>
            <tr><th>ID</th><th>Immatriculation</th></tr>
            <?php if (empty($slave_rows)): ?>
                <tr><td colspan="2" style="text-align:center;">Aucune donnée (en attente de réplication...)</td></tr>
            <?php else: ?>
                <?php foreach ($slave_rows as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['immatriculation']) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>
<p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>