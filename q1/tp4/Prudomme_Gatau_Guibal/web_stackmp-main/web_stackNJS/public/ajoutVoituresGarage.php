<?php
$db = new SQLite3('garage.db');
$stmt = $db->prepare('INSERT INTO voiture (attribut) VALUES (:attr)');
$voitures = ['Peugeot 208', 'Renault Clio', 'Citroen C3', 'Toyota Yaris'];
foreach ($voitures as $v) {
    $stmt->bindValue(':attr', $v, SQLITE3_TEXT);
    $stmt->execute();
}
echo "<p style='color:green'>4 voitures ont été ajoutées au garage.</p>";
?>
