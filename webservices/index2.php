<?php

// Connessione al database
$servername = "localhost";
$username = "utente2";
$password = "1234";
$dbname = "server_web";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione
if ($conn->connect_error) {
    http_response_code(500); 
    die("Failed to connect: " . $conn->connect_error);
}

// Validazione dei dati in input
function validateData($data) {
    if (
        !isset($data['nome']) || !is_string($data['nome']) ||
        !isset($data['cognome']) || !is_string($data['cognome']) ||
        !isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL) ||
        !isset($data['eta']) || !is_numeric($data['eta']) ||
        !isset($data['tel']) || !is_numeric($data['tel'])
    ) {
        return false;
    }
    return true;
}

$array = explode('/', $_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (count($array) == 3 && $array[2] != '') {
        // Se è specificato un ID nella richiesta GET
        $id = $array[2];
        $sql = "SELECT * FROM dati WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            http_response_code(200); 
            echo json_encode($row);
        } else {
            http_response_code(404); 
            
        }
    } elseif (count($array) == 3 && $array[2] == '') {
        // Se non è specificato un ID nella richiesta GET
        $sql = "SELECT * FROM dati";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            http_response_code(200);
            echo json_encode($rows);
        } else {
            http_response_code(404); 
            
        }
    } else {
        // Se il metodo HTTP non è GET
        http_response_code(405); 
        
    }
} elseif ($method == 'POST') {
    // Esegui l'inserimento dei dati
    $data = json_decode(file_get_contents("php://input"), true);

    // Verifica se i dati sono stati inviati correttamente
    if (!empty($data) && validateData($data)) {
        // Esegui l'inserimento nel database
        $sql = "INSERT INTO dati (nome, cognome, email, eta, tel) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssis", $data['nome'], $data['cognome'], $data['email'], $data['eta'], $data['tel']);

        if ($stmt->execute()) {
            http_response_code(201);
            
        } else {
            http_response_code(500); 
            
        }
    } else {
        http_response_code(400); 
        
    }
} elseif ($method == 'PUT') {
    // Esegui l'aggiornamento dei dati
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Verifica se l'ID è stato fornito
    if (count($array) == 3 && $array[2] != '') {
        $id = $array[2];
        
        // Esegui l'aggiornamento nel database
        if (!empty($data) && validateData($data)) {
            $sql = "UPDATE dati SET nome=?, cognome=?, email=?, eta=?, tel=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $data['nome'], $data['cognome'], $data['email'], $data['eta'], $data['tel'], $id);

            if ($stmt->execute()) {
                http_response_code(200);
                
            } else {
                http_response_code(500); 
               
            }
        } else {
            http_response_code(400); 
            
        }
    } else {
        http_response_code(400); 
        
    }
} elseif ($method == 'DELETE') {
    // Esegui la cancellazione dei dati
    if (count($array) == 3 && $array[2] != '') {
        $id = $array[2];
        
        // Esegui la cancellazione nel database
        $sql = "DELETE FROM dati WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            http_response_code(200);
           
        } else {
            http_response_code(500); 
            
        }
    } else {
        http_response_code(400); 
        
    }
} else {
    // Se il metodo HTTP non è supportato
    http_response_code(405); 
    
}

$conn->close();

?>