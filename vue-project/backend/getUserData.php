<?php
// CORS Header hinzufügen
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

header('Content-Type: application/json');

// Datenbankverbindung und Abfrage
require('db_connection.php');

session_start();

// Überprüfen, ob der Benutzer eingeloggt ist (über Session-ID)
if (isset($_SESSION['id'])) {
    // SQL-Abfrage, um Benutzerdaten basierend auf der Session-ID zu holen
    $sql = "SELECT id, firstname, lastname, username, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($id, $firstname, $lastname, $username, $email);
    $stmt->fetch();

    if ($id) {
        echo json_encode([
            "user" => [
                "id" => $id,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "username" => $username,
                "email" => $email,
            ]
        ]);
    } else {
        echo json_encode(["message" => "Kein Benutzer gefunden."]);
    }

    $stmt->close();
} else {
    echo json_encode(["message" => "Nicht eingeloggt."]);
}

$conn->close();
?>
