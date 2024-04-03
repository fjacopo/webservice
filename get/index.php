<?php

// Configurazione del database
$servername = "localhost";
$username = "programma";
$password = "12345";
$dbname = "eventi";

// Creazione della connessione al database
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione al database
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}

// Route per ottenere tutti i dati
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/dati') {
    $sql = "SELECT * FROM eventi";
    $result = $conn->query($sql);

    if ($result === false) {
        http_response_code(500);
        echo json_encode(["error" => $conn->error]);
    } else {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        echo json_encode($rows);
    }
}

// Route per ottenere un record specifico
if ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/^\/dati\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
    $id = $matches[1];
    $sql = "SELECT * FROM eventi WHERE id = $id";
    $result = $conn->query($sql);

    if ($result === false) {
        http_response_code(500);
        echo json_encode(["error" => $conn->error]);
    } elseif ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Record non trovato"]);
    } else {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    }
}

// Chiusura della connessione al database
$conn->close();