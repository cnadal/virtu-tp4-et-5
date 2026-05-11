<?php
session_start();
require_once 'db.php';

$host = 'mysql_master';
$dbname = getenv('MYSQL_DATABASE') ?: 'app_db';
$user = getenv('MYSQL_USER') ?: 'app_user';
$pass = getenv('MYSQL_PASSWORD') ?: 'app_password';

$pdo = getDbConnection($host, $dbname, $user, $pass);
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $headers = "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\nFrom: noreply@tp5.local\r\n";
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("INSERT INTO voitures (marque, modele) VALUES (?, ?)");
        $stmt->execute([$_POST['marque'], $_POST['modele']]);
        $message = "Voiture ajoutée avec succès !";
        
        mail("admin@tp5.local", "=?UTF-8?B?" . base64_encode("Nouvelle Voiture") . "?=", "Une nouvelle voiture a été ajoutée : " . $_POST['marque'] . " " . $_POST['modele'], $headers);
        
    } elseif (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM voitures WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = "Voiture supprimée avec succès !";
        
        mail("admin@tp5.local", "=?UTF-8?B?" . base64_encode("Voiture Supprimée") . "?=", "Une voiture a été supprimée (ID: " . $_POST['id'] . ")", $headers);
    }
}

$voitures = $pdo->query("SELECT * FROM voitures")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Base de données Master</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; }
        .container { background-color: white; padding: 20px; border-radius: 8px; max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #28a745; color: white; }
        a { text-decoration: none; color: #007bff; display: inline-block; margin-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        input[type="text"] { padding: 8px; width: 200px; }
        button { padding: 8px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button.delete { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">⬅ Retour à l'accueil</a>
        <h1>Gérer les voitures (Base Master - Écriture)</h1>
        
        <?php if ($message): ?>
            <p style="color: green;"><strong><?php echo htmlspecialchars($message); ?></strong></p>
        <?php endif; ?>
        
        <form method="POST">
            <h3>Ajouter une voiture</h3>
            <div class="form-group">
                <input type="text" name="marque" placeholder="Marque" required>
                <input type="text" name="modele" placeholder="Modèle" required>
                <button type="submit" name="add">Ajouter</button>
            </div>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Action</th>
            </tr>
            <?php foreach ($voitures as $v): ?>
            <tr>
                <td><?php echo htmlspecialchars($v['id']); ?></td>
                <td><?php echo htmlspecialchars($v['marque']); ?></td>
                <td><?php echo htmlspecialchars($v['modele']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($v['id']); ?>">
                        <button type="submit" name="delete" class="delete">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
