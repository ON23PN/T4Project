<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Credentials: true');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob die erforderlichen POST-Daten vorhanden sind
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        echo json_encode(["message" => "POST-Daten fehlen."]);
        exit;
    }

    // Eingabewerte aus POST holen und bereinigen
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // SQL-Abfrage, um Benutzerdaten basierend auf dem Benutzernamen abzurufen
    $sql = "SELECT id, firstname, lastname, username, email, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["message" => "Fehler bei der SQL-Abfrage: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Benutzerinformationen in Variablen speichern
    $stmt->bind_result($id, $firstname, $lastname, $username, $fetchedEmail, $storedPassword);
    $stmt->fetch();

    // Prüfen, ob der Benutzer existiert und das Passwort korrekt ist
    if ($username && password_verify($password, $storedPassword)) {
        session_start();

        // Sicherheitsmaßnahme: Session-ID regenerieren, um Session-Hijacking zu verhindern
        session_regenerate_id(true);

        // Session-Daten setzen
        $_SESSION['id'] = $id;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $fetchedEmail;

        // Erfolgreiche Login-Antwort
        echo json_encode([
            "message" => "Login erfolgreich!",
            "user" => [
                "id" => $id,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "username" => $username,
                "email" => $fetchedEmail
            ]
        ]);
    } else {
        // Fehler: Benutzername oder Passwort ungültig
        echo json_encode(["message" => "Ungültiger Benutzername oder Passwort."]);
    }

    // Ressourcen freigeben
    $stmt->close();
    $conn->close();
} else {
    // Fehler: Ungültige Anfrage
    echo json_encode(["message" => "Ungültige Anfrage."]);
}
?>
