<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Credentials: true');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require('db_connection.php');

session_start();

// Prüfen, ob der Benutzer eingeloggt ist (Session existiert)
if (!isset($_SESSION['id'])) {
    echo json_encode(["message" => "Nicht eingeloggt"]);
    exit;
}

// Daten aus dem JSON-Body des POST-Requests holen
$data = json_decode(file_get_contents("php://input"), true);

// Prüfen, ob die Daten korrekt erhalten wurden
if (!isset($data['amount']) || !isset($data['description'])) {
    echo json_encode(["message" => "Daten fehlen"]);
    exit;
}

// Daten extrahieren
$amount = $data['amount'];
$description = $data['description'];
$userId = $_SESSION['id']; // Benutzer-ID aus der Session holen

// Sicherstellen, dass der Betrag als Zahl gespeichert wird (float)
$amount = (float)$amount;

// SQL-Query zum Einfügen der Transaktion
$sql = "INSERT INTO transactions (user_id, amount, description) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["message" => "Fehler bei der SQL-Abfrage"]);
    exit;
}

$stmt->bind_param("ids", $userId, $amount, $description);  // Binden der Parameter: i = integer, d = double (float), s = string
$stmt->execute();

// Prüfen, ob die Transaktion erfolgreich eingefügt wurde
if ($stmt->affected_rows > 0) {
    echo json_encode(["message" => "Transaktion hinzugefügt"]);
} else {
    echo json_encode(["message" => "Fehler beim Hinzufügen der Transaktion"]);
}

$stmt->close();
$conn->close();
?>



