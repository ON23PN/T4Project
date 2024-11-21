<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

// Deine Datenbankverbindung
require('db_connection.php');

// Die ID des Benutzers aus der Session holen
session_start();
if (!isset($_SESSION['id'])) {
    echo json_encode(["message" => "Nicht eingeloggt"]);
    exit;
}

$userId = $_SESSION['id'];

// SQL-Abfrage, um die Transaktionen des Benutzers abzurufen
// Hier wird auch die 'description' mit abgefragt
$sql = "SELECT id, amount, description FROM transactions WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    // Sicherstellen, dass der Betrag als float gespeichert wird
    $row['amount'] = (float)$row['amount']; // Umwandlung in float
    $transactions[] = $row;
}

// JSON mit den Transaktionen und der Beschreibung ausgeben
echo json_encode(["transactions" => $transactions]);

$stmt->close();
$conn->close();
?>
