<?php
$host = 'mysql_db';
$db = 'garageM';
$user = 'root';
$pass = getenv('MYSQL_ROOT_PASSWORD') ?: 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $immat = chr(rand(65,90)).chr(rand(65,90))."-".rand(100,999)."-".chr(rand(65,90));
    $couleurs = array('Rouge', 'Bleu', 'Noir', 'Gris', 'Blanc', 'Jaune', 'Vert');
    $couleur = $couleurs[array_rand($couleurs)];
    $km = rand(0, 200000);

    $stmt = $pdo->prepare("INSERT INTO voitures (immatriculation, couleur, km) VALUES (?, ?, ?)");
    $stmt->execute([$immat, $couleur, $km]);

    echo "<h3>Voiture ajoutée avec succès</h3>";
    echo "Immatriculation : $immat <br>";
    echo "Couleur : $couleur <br>";
    echo "Kilométrage : $km km <br>";
    echo "<br><a href='listeVoitures.php'>Voir la liste des voitures</a>";
} catch (PDOException $e) {
    echo "Erreur (assurez-vous d'avoir créé la BDD): " . $e->getMessage();
    echo "<br><a href='bd/creationBDD.php'>Créer la BDD</a>";
}
?>
