<?php
try {
    // Connexion initiale à la BDD par défaut pour créer la nouvelle BDD
    $db = new PDO('pgsql:host=postgres;dbname=postgres', 'postgres', 'mdpRoot23');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // PostgreSQL ne permet pas de CREATE DATABASE dans une transaction ou via une requête préparée basique.
    // On lance la création si elle n'existe pas (en contournant l'absence de IF NOT EXISTS standard).
    $result = $db->query("SELECT 1 FROM pg_database WHERE datname = 'garage'")->fetch();
    if (!$result) {
        $db->exec("CREATE DATABASE garage");
        echo "<p style='color:green'>Base de données 'garage' créée avec succès sous PostgreSQL !</p>";
    } else {
        echo "<p style='color:blue'>La base de données 'garage' existe déjà.</p>";
    }
    
    // Reconnexion à la nouvelle BDD pour y créer la table
    $dbGarage = new PDO('pgsql:host=postgres;dbname=garage', 'postgres', 'mdpRoot23');
    $dbGarage->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbGarage->exec("CREATE TABLE IF NOT EXISTS voiture (id SERIAL PRIMARY KEY, attribut VARCHAR(255))");
    echo "<p style='color:green'>Table 'voiture' créée avec succès (ou déjà existante).</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur de connexion : " . $e->getMessage() . "</p>";
}
?>
