<?php
header('Content-Type: application/json');
require('db_connection.php');

// Prüfen, ob die Anfrage eine POST-Anfrage ist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eingabewerte aus POST holen und bereinigen
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Klartext Passwort
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']); // Hinzugefügt

    // Eingabevalidierung
    if (empty($email) || empty($password) || empty($firstname) || empty($lastname) || empty($username)) {
        echo json_encode(["message" => "Bitte alle Felder ausfüllen."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["message" => "Ungültige E-Mail-Adresse."]);
        exit;
    }

    // Überprüfen, ob die E-Mail bereits registriert wurde
    $sql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["message" => "Datenbankfehler: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // E-Mail existiert bereits
        echo json_encode(["message" => "Diese E-Mail-Adresse ist bereits registriert."]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Passwort verschlüsseln
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Benutzer in der Datenbank speichern
    $insertSql = "INSERT INTO users (email, password, firstname, lastname, username) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    if (!$insertStmt) {
        echo json_encode(["message" => "Datenbankfehler: " . $conn->error]);
        exit;
    }
    $insertStmt->bind_param("sssss", $email, $hashedPassword, $firstname, $lastname, $username);

    if ($insertStmt->execute()) {
        // Benutzer erfolgreich registriert, Sitzung starten
        session_start(); 
        $_SESSION['email'] = $email;  
        $_SESSION['firstname'] = $firstname; 
        $_SESSION['lastname'] = $lastname; 
        $_SESSION['username'] = $username; 

        echo json_encode(["message" => "Registrierung erfolgreich!"]);
    } else {
        // Fehler bei der Registrierung
        echo json_encode(["message" => "Fehler bei der Registrierung: " . $insertStmt->error]);
    }

    // Ressourcen freigeben
    $insertStmt->close();
    $conn->close();
} else {
    echo json_encode(["message" => "Ungültige Anfrage."]);
}
?>
