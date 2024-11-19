<?php
header('Content-Type: application/json');
require('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eingabewerte aus POST holen
    $email = $_POST['email'];
    $password = $_POST['password']; // Klartext Passwort
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    // Überprüfen, ob die E-Mail bereits registriert wurde
    $sql = "SELECT email FROM users WHERE email = ?"; // Tabelle 'users' verwenden
    $stmt = $conn->prepare($sql);
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

    // Benutzer in der Datenbank speichern
    $insertSql = "INSERT INTO users (email, password, firstname, lastname) VALUES (?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ssss", $email, $password, $firstname, $lastname);
    
    if ($insertStmt->execute()) {
        // Benutzer erfolgreich registriert, Sitzung starten
        session_start(); 
        $_SESSION['email'] = $email;  
        $_SESSION['firstname'] = $firstname; 
        $_SESSION['lastname'] = $lastname; 

        echo json_encode(["message" => "Registrierung erfolgreich!"]);
    } else {
        //Fehler bei der Registrierung
        echo json_encode(["message" => "Fehler bei der Registrierung."]);
    }

    // Ressourcen freigeben
    $insertStmt->close();
    $conn->close();
}
?>
