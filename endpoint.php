<?php
// endpoint.php

require_once 'config.php';

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
    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO floris_shop_db (name, description, price) VALUES (:name, :description, :price)");

        // Bind parameters
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            $success_message = 'Item inserted successfully with ID ' . $conn->lastInsertId();
            write_log($success_message);
            echo json_encode(['success' => true, 'message' => $success_message]);
        } else {
            $error_info = $stmt->errorInfo();
            http_response_code(500);
            $error_message = 'Execute failed: ' . $error_info[2];
            write_log($error_message);
            echo json_encode(['error' => $error_message]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        $error_message = 'Database error: ' . $e->getMessage();
        write_log($error_message);
        echo json_encode(['error' => $error_message]);
    }
} else {
    http_response_code(400);
    $error_message = 'Invalid JSON data received';
    write_log($error_message);
    echo json_encode(['error' => $error_message]);
}
?>