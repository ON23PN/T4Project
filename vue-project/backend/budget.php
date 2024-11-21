<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Credentials: true');

require('db_connection.php');

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['id'])) {
    echo json_encode(["message" => "Bitte melden Sie sich an."]);
    exit;
}

$userId = $_SESSION['id']; // Benutzer-ID aus der Session holen

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Hole die Budget-Daten für den aktuellen Benutzer
    $sql = "SELECT * FROM budget WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $budgets = [];
    while ($row = $result->fetch_assoc()) {
        $budgets[] = $row;
    }

    echo json_encode(["budgets" => $budgets]);

    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Budget-Daten für den aktuellen Benutzer speichern
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    $sql = "INSERT INTO budget (user_id, description, amount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $userId, $description, $amount);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Budget-Daten gespeichert."]);
    } else {
        echo json_encode(["message" => "Fehler beim Speichern der Budget-Daten."]);
    }

    $stmt->close();
    $conn->close();
}
?>
