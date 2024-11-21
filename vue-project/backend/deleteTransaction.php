<?php
// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "budget_manager"; 

try {
    // PDO-Verbindung zur MySQL-Datenbank herstellen
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Fehler im PDO-Modus auf Ausnahmen setzen
} catch (PDOException $e) {
    // Bei einem Fehler in der Datenbankverbindung, Fehler ausgeben und abbrechen
    echo json_encode(['error' => 'Datenbankverbindung fehlgeschlagen: ' . $e->getMessage()]);
    exit();
}

// Hole die ID der zu löschenden Transaktion
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // SQL-Abfrage, um die Transaktion zu löschen
    $stmt = $pdo->prepare('DELETE FROM transactions WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Versuche die Transaktion zu löschen und gebe eine JSON-Antwort zurück
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Transaktion gelöscht']);
    } else {
        echo json_encode(['error' => 'Fehler beim Löschen der Transaktion']);
    }
} else {
    echo json_encode(['error' => 'Keine ID angegeben']);
}
?>
