<?php
try {
    $db = new PDO('pgsql:host=postgres;dbname=garage', 'postgres', 'mdpRoot23');
    $results = $db->query('SELECT * FROM voiture');
    echo "<table border='1' style='margin:0 auto; border-collapse: collapse; text-align: left;'>";
    echo "<tr><th style='padding: 8px;'>ID</th><th style='padding: 8px;'>Attribut</th></tr>";
    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td style='padding: 8px;'>" . htmlspecialchars((string)$row['id']) . "</td><td style='padding: 8px;'>" . htmlspecialchars((string)$row['attribut']) . "</td></tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur : " . $e->getMessage() . "</p>";
}
?>
