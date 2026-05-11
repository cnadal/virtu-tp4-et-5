<?php
try {
    $db = new PDO('mysql:host=mysql', 'root', 'mdpRoot23');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("CREATE DATABASE IF NOT EXISTS garage");
    $db->exec("USE garage");
    $db->exec("CREATE TABLE IF NOT EXISTS voiture (id INT AUTO_INCREMENT PRIMARY KEY, attribut VARCHAR(255))");
    echo "<p style='color:green'>Base de données 'garage' et table 'voiture' créées avec succès sous MySQL !</p>";
}
catch (PDOException $e) {
    echo "<p style='color:red'>Erreur de connexion : " . $e->getMessage() . "</p>";
}
?>
