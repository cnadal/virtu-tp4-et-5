<?php
$db = new SQLite3('garage.db');
$db->exec("CREATE TABLE IF NOT EXISTS voiture (id INTEGER PRIMARY KEY AUTOINCREMENT, attribut TEXT)");
echo "<p style='color:green'>Base de données 'garage.db' et table 'voiture' créées avec succès !</p>";
?>
