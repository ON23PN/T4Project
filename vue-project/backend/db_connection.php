<?php
$servername = "localhost";
$username = "root"; 
$password = "";     
$database = "budget_manager"; 

// Verbindung zum MySQL-Server herstellen
$conn = new mysqli($servername, $username, $password);

// Überprüfen, ob die Verbindung erfolgreich war
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Datenbank erstellen, falls sie nicht existiert
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === FALSE) {
    die("Fehler beim Erstellen der Datenbank: " . $conn->error);
}

// Datenbank auswählen
$conn->select_db($database);

// Tabelle `users` erstellen, falls sie nicht existiert
$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
if ($conn->query($sql) === FALSE) {
    die("Fehler beim Erstellen der Tabelle `users`: " . $conn->error);
}

// Tabelle `transactions` erstellen, falls sie nicht existiert
$sql = "
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
if ($conn->query($sql) === FALSE) {
    die("Fehler beim Erstellen der Tabelle `transactions`: " . $conn->error);
}
?>
