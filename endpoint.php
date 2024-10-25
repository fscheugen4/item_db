<?php
// endpoint.php
// flori

header('Content-Type: application/json');

// Allow requests from kleinanzeigen.de
header('Access-Control-Allow-Origin: https://www.kleinanzeigen.de');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    // Connect to the database
    $mysqli = new mysqli('localhost', 'item_db', 'haxx0r', 'item_db');

    if ($mysqli->connect_error) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }

    // Prepare and bind
    $stmt = $mysqli->prepare("INSERT INTO items (name, description, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $data['name'], $data['description'], $data['price']);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database insertion failed']);
    }

    $stmt->close();
    $mysqli->close();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
}
?>