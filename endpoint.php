<?php
// endpoint.php

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Allow requests from kleinanzeigen.de
header('Access-Control-Allow-Origin: https://www.kleinanzeigen.de');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log file path (ensure the web server has write permissions)
$log_file = '/opt/homebrew/var/www/item_db/logfile.log';

// Function to write logs
function write_log($message) {
    global $log_file;
    $date = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$date] $message\n", FILE_APPEND);
}

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Log the received data
write_log("Received data: " . $json);

if ($data) {
    // Connect to the database
    $mysqli = new mysqli('localhost', 'item_db', 'haxx0r', 'shop_db');

    if ($mysqli->connect_error) {
        http_response_code(500);
        $error_message = 'Database connection failed: ' . $mysqli->connect_error;
        write_log($error_message);
        echo json_encode(['error' => $error_message]);
        exit();
    }

    // Prepare and bind
    $stmt = $mysqli->prepare("INSERT INTO items (name, description, price) VALUES (?, ?, ?)");
    if (!$stmt) {
        http_response_code(500);
        $error_message = 'Prepare failed: ' . $mysqli->error;
        write_log($error_message);
        echo json_encode(['error' => $error_message]);
        exit();
    }

    $stmt->bind_param("ssd", $data['name'], $data['description'], $data['price']);

    if ($stmt->execute()) {
        $success_message = 'Item inserted successfully with ID ' . $stmt->insert_id;
        write_log($success_message);
        echo json_encode(['success' => true, 'message' => $success_message]);
    } else {
        http_response_code(500);
        $error_message = 'Execute failed: ' . $stmt->error;
        write_log($error_message);
        echo json_encode(['error' => $error_message]);
    }

    $stmt->close();
    $mysqli->close();
} else {
    http_response_code(400);
    $error_message = 'Invalid JSON data received';
    write_log($error_message);
    echo json_encode(['error' => $error_message]);
}
?>