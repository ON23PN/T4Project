<?php
session_start();
session_unset(); // Alle Session-Daten löschen
session_destroy(); // Session zerstören

echo json_encode(["message" => "Erfolgreich abgemeldet."]);
?>
