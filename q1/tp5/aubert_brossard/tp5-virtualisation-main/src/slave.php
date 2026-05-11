<?php
session_start();
if (!isset($_SESSION['authenticated'])) {
    header("Location: index.php");
    exit;
}

try {
    // Connexion à la BDD Slave (Lecture seule théoriquement)
    $pdo = new PDO('mysql:host=mysql_slave;dbname=tp5_db;charset=utf8', 'root', 'rootpwd');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM voitures");
    $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur de connexion au Slave : " . $e->getMessage();
    $hint = "Avez-vous bien activé la réplication master-slave avec CHANGE MASTER TO... ?";
}
?>
<!DOCTYPE html>
<html>
<head><title>Slave - Voitures</title></head>
<body>
    <h1>Lecture via Slave DB</h1>
    <a href="index.php">Retour à l'accueil</a> | <a href="master.php">Aller au Master</a>
    <hr>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= $error ?></p>
        <p><?= $hint ?></p>
    <?php else: ?>
        <p><em>(Ces données proviennent de la base synchronisée Slave et ne peuvent pas être modifiées d'ici)</em></p>
        <table border="1">
            <tr><th>ID</th><th>Marque</th><th>Modèle</th><th>Année</th></tr>
            <?php foreach ($voitures as $v): ?>
                <tr>
                    <td><?= $v['id'] ?></td><td><?= htmlspecialchars($v['marque']) ?></td>
                    <td><?= htmlspecialchars($v['modele']) ?></td><td><?= $v['annee'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
