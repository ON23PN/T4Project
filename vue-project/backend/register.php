<?php
header('Content-Type: application/json');
require('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eingabewerte aus POST holen
    $userEmail = $_POST['email'];
    $userPassword = $_POST['password']; // Klartext Passwort
    $userFirstname = $_POST['firstname'];
    $userLastname = $_POST['lastname'];

    // Validierung: Sicherstellen, dass alle Felder vorhanden sind
    if (empty($userEmail) || empty($userPassword) || empty($userFirstname) || empty($userLastname)) {
        echo json_encode(["message" => "Alle Felder müssen ausgefüllt sein."]);
        exit;
    }

    // Überprüfen, ob die E-Mail bereits registriert wurde
    $checkEmailSql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailSql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // E-Mail existiert bereits
        echo json_encode(["message" => "Diese E-Mail-Adresse ist bereits registriert."]);
        $stmt->close();
        $conn->close();
        exit;
    }


    // Benutzer in der Datenbank speichern
    $insertUserSql = "INSERT INTO users (email, password, firstname, lastname) VALUES (?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertUserSql);
    $insertStmt->bind_param("ssss", $userEmail, $hashedPassword, $userFirstname, $userLastname);

    if ($insertStmt->execute()) {
        // Benutzer erfolgreich registriert, Sitzung starten
        session_start();
        $_SESSION['email'] = $userEmail;
        $_SESSION['firstname'] = $userFirstname;
        $_SESSION['lastname'] = $userLastname;

        echo json_encode(["message" => "Registrierung erfolgreich!"]);
    } else {
        // Fehler bei der Registrierung
        echo json_encode(["message" => "Es gab einen Fehler bei der Registrierung."]);
    }

    // Ressourcen freigeben
    $insertStmt->close();
    $conn->close();
} else {
    // Falls die Anfrage keine POST-Anfrage ist
    echo json_encode(["message" => "Ungültige Anfrage."]);
}
?>

