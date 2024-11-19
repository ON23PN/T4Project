<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Credentials: true');

ini_set('display_errors', 1);
error_reporting(E_ALL); // Alle Fehler anzeigen, um beim Debugging zu helfen

// Stelle die Verbindung zur Datenbank her
require('db_connection.php');

// Überprüfen, ob die Anfrage eine POST-Anfrage ist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Überprüfen, ob die notwendigen POST-Daten (E-Mail und Passwort) gesendet wurden
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        echo json_encode(["message" => "POST-Daten fehlen."]);
        exit;
    }

    // Holen der E-Mail und des Passworts aus den POST-Daten
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL-Abfrage, um den Benutzer anhand der E-Mail-Adresse zu finden
    $sql = "SELECT email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    // Überprüfen, ob die SQL-Abfrage erfolgreich vorbereitet wurde
    if (!$stmt) {
        echo json_encode(["message" => "Fehler bei der SQL-Abfrage-Vorbereitung: " . $conn->error]);
        exit;
    }

    // Binde die E-Mail-Parameter und führe die Abfrage aus
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Überprüfen, ob ein Fehler während der Abfrage-Ausführung aufgetreten ist
    if ($stmt->errno) {
        echo json_encode(["message" => "Fehler bei der SQL-Abfrage-Ausführung: " . $stmt->error]);
        exit;
    }

    // Die E-Mail und das Passwort des Benutzers aus der Datenbank abfragen
    $stmt->bind_result($fetchedEmail, $storedPassword);
    $stmt->fetch();

    // Überprüfen, ob ein Benutzer mit der angegebenen E-Mail existiert und ob das Passwort korrekt ist
    if ($fetchedEmail && $storedPassword === $password) {
        session_start(); // Startet eine neue PHP-Session
        $_SESSION['email'] = $email; // Speichert die E-Mail-Adresse in der Session
        echo json_encode(["message" => "Login erfolgreich!"]);
    } else {
        echo json_encode(["message" => "Ungültige E-Mail-Adresse oder Passwort."]);
    }

    // Schließe das Prepared Statement und die Datenbankverbindung
    $stmt->close();
    $conn->close();
} else {
    // Wenn keine POST-Anfrage empfangen wurde, gebe eine Fehlermeldung zurück
    echo json_encode(["message" => "Ungültige Anfrage."]);
}
?>
