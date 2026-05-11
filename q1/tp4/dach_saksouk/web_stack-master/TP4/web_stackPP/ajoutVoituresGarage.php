<?php
$host = 'postgres_db';
$db = 'garageP';
$user = getenv('POSTGRES_USER') ?: 'admin';
$pass = getenv('POSTGRES_PASSWORD') ?: '1234';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $immat = chr(rand(65,90)).chr(rand(65,90))."-".rand(100,999)."-".chr(rand(65,90));
    $couleurs = array('Rouge', 'Bleu', 'Noir', 'Gris', 'Blanc', 'Jaune', 'Vert');
    $couleur = $couleurs[array_rand($couleurs)];
    $km = rand(0, 200000);

    $stmt = $pdo->prepare("INSERT INTO voitures (immatriculation, couleur, km) VALUES (?, ?, ?)");
    $stmt->execute([$immat, $couleur, $km]);

    echo "<h3>Voiture ajoutée avec succès (PostgreSQL)</h3>";
    echo "Immatriculation : $immat <br>";
    echo "Couleur : $couleur <br>";
    echo "Kilométrage : $km km <br>";
    echo "<br><a href='listeVoitures.php'>Voir la liste des voitures</a>";
} catch (PDOException $e) {
    echo "Erreur (assurez-vous d'avoir créé la BDD): " . $e->getMessage();
    echo "<br><a href='bd/creationBDD.php'>Créer la BDD</a>";
}
?>
