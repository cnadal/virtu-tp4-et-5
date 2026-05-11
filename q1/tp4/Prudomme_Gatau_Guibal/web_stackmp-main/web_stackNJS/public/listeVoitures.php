<?php
$db = new SQLite3('garage.db');
$results = $db->query('SELECT * FROM voiture');
echo "<table border='1' style='margin:0 auto; border-collapse: collapse; text-align: left;'>";
echo "<tr><th style='padding: 8px;'>ID</th><th style='padding: 8px;'>Attribut</th></tr>";
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    echo "<tr><td style='padding: 8px;'>" . $row['id'] . "</td><td style='padding: 8px;'>" . htmlspecialchars($row['attribut']) . "</td></tr>";
}
echo "</table>";
?>
