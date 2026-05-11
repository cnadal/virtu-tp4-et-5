<?php
session_start();
if (!isset($_SESSION['authenticated'])) {
    header("Location: index.php");
    exit;
}

try {
    // Connexion à la BDD Master
    $pdo = new PDO('mysql:host=mysql_master;dbname=tp5_db;charset=utf8', 'root', 'rootpwd');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Si on soumet un formulaire pour ajouter
    if (isset($_POST['ajouter'])) {
        $stmt = $pdo->prepare("INSERT INTO voitures (marque, modele, annee) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['marque'], $_POST['modele'], $_POST['annee']]);
        header("Location: master.php");
        exit;
    }

    // Si on soumet un formulaire pour supprimer
    if (isset($_GET['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM voitures WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: master.php");
        exit;
    }

    $stmt = $pdo->query("SELECT * FROM voitures");
    $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur de connexion : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head><title>Master - Voitures</title></head>
<body>
    <h1>Administration Master DB</h1>
    <a href="index.php">Retour à l'accueil</a> | <a href="slave.php">Voir Slave</a>
    <hr>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php else: ?>
        <h3>Liste des Voitures (Master)</h3>
        <table border="1">
            <tr><th>ID</th><th>Marque</th><th>Modèle</th><th>Année</th><th>Action</th></tr>
            <?php foreach ($voitures as $v): ?>
                <tr>
                    <td><?= $v['id'] ?></td><td><?= htmlspecialchars($v['marque']) ?></td>
                    <td><?= htmlspecialchars($v['modele']) ?></td><td><?= $v['annee'] ?></td>
                    <td><a href="?delete=<?= $v['id'] ?>">Supprimer</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Ajouter une voiture</h3>
        <form method="POST">
            <input type="text" name="marque" placeholder="Marque" required>
            <input type="text" name="modele" placeholder="Modèle" required>
            <input type="number" name="annee" placeholder="Année" required>
            <button type="submit" name="ajouter">Ajouter (Master uniquement)</button>
        </form>
    <?php endif; ?>
</body>
</html>
