<?php
try {
    $db = new PDO('pgsql:host=postgres;dbname=garage', 'postgres', 'mdpRoot23');
    $stmt = $db->prepare('INSERT INTO voiture (attribut) VALUES (:attr)');
    $voitures = ['Peugeot 208', 'Renault Clio', 'Citroen C3', 'Toyota Yaris'];
    foreach ($voitures as $v) {
        $stmt->bindValue(':attr', $v, PDO::PARAM_STR);
        $stmt->execute();
    }
    echo "<p style='color:green'>4 voitures ont été ajoutées au garage sous PostgreSQL.</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur : " . $e->getMessage() . "</p>";
}
?>
