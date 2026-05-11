<?php
$pdo = new PDO("mysql:host=mysql;dbname=garage", "user", "password");
$result = $pdo->query("SELECT * FROM voitures");

foreach ($result as $row) {
    echo $row['nom'] . "<br>";
}
?>
