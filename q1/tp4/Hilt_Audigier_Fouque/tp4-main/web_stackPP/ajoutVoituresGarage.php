<?php
$host = 'postgres';
$db   = 'garage';
$user = 'postgres';
$pass = 'postgres';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO voitures (marque, modele, annee) VALUES 
        ('Peugeot', '208', 2020),
        ('Renault', 'Clio', 2019),
        ('Volkswagen', 'Golf', 2021)
    ";

    $pdo->exec($sql);
    echo "Voitures ajoutées avec succès dans le garage !<br>";
    echo "<a href='listeVoitures.php'>Voir la liste des voitures</a>";

} catch (PDOException $e) {
    echo "Erreur d'insertion : " . $e->getMessage();
}
?>
