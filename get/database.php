<?php
// Configurazione di connessione al database
$servername = "localhost";
$username = "programma";
$password = "1234";
$dbname = "eventi";

// Creazione della connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->error);
}
?>