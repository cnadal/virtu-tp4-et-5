<?php
session_start();
require_once 'db.php';

$host = 'mysql_slave';
$dbname = getenv('MYSQL_DATABASE') ?: 'app_db';
$user = getenv('MYSQL_USER') ?: 'app_user';
$pass = getenv('MYSQL_PASSWORD') ?: 'app_password';

$pdo = getDbConnection($host, $dbname, $user, $pass);

$pdo->exec("SET NAMES 'utf8mb4'");

$voitures = $pdo->query("SELECT * FROM voitures")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Base de données Slave</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; }
        .container { background-color: white; padding: 20px; border-radius: 8px; max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #6c757d; color: white; }
        a { text-decoration: none; color: #007bff; display: inline-block; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">⬅ Retour à l'accueil</a>
        <h1>Voir les voitures (Base Slave - Lecture Seule)</h1>
        
        <p>Les données affichées ici proviennent du conteneur <strong>mysql_slave</strong>, répliquées depuis le <strong>mysql_master</strong>.</p>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Marque</th>
                <th>Modèle</th>
            </tr>
            <?php foreach ($voitures as $v): ?>
            <tr>
                <td><?php echo htmlspecialchars($v['id']); ?></td>
                <td><?php echo htmlspecialchars($v['marque']); ?></td>
                <td><?php echo htmlspecialchars($v['modele']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
