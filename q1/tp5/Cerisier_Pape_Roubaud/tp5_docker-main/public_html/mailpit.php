<?php
session_start();

$apiBase = "http://mail_test:8025/api/v1";

// Suppression d'un mail
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $ch = curl_init("$apiBase/messages");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["IDs" => [$id]]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
    header("Location: mailpit.php");
    exit;
}

$response = @file_get_contents("$apiBase/messages");
$messages = [];
if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['messages'])) {
        $messages = $data['messages'];
    }
} else {
    $error = "Impossible de se connecter à l'API Mailpit.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emails - Mailpit</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; }
        .container { background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        a { text-decoration: none; color: #007bff; margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">⬅ Retour à l'accueil</a>
        <h1>Boîte de réception (Mailpit)</h1>
        
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>De</th>
                    <th>Sujet</th>
                    <th>Aperçu</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($msg['Created']))); ?></td>
                    <td><?php echo htmlspecialchars($msg['From']['Address']); ?></td>
                    <td><?php echo htmlspecialchars($msg['Subject']); ?></td>
                    <td><?php echo htmlspecialchars($msg['Snippet']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($msg['ID']); ?>">
                            <button type="submit" style="background:#dc3545;color:white;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;">🗑 Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($messages)): ?>
                <tr>
                    <td colspan="5">Aucun email reçu.</td>
                </tr>
                <?php endif; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
