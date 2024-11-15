<?php
// endpoint.php

// Enable error reporting for debugging (Disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Allow requests from kleinanzeigen.de
header('Access-Control-Allow-Origin: https://www.kleinanzeigen.de');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Include config file
$config = require 'config.php';

// Log file path (ensure the web server has write permissions)
$log_file = __DIR__ . '/logs/logfile.log';

// Function to write logs
function write_log($message) {
    global $log_file;
    $date = date('Y-m-d H:i:s');

    // Ensure the directory exists
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }

    file_put_contents($log_file, "[$date] $message\n", FILE_APPEND);
}

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Log the received data
write_log("Received data: " . $json);

if ($data) {
    try {
        // Create PDO instance
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO floris_shop_db (name, description, price, image, kleinanzeigen_state, kleinanzeigen_date) VALUES (:name, :description, :price, :image, :kleinanzeigen_state, :kleinanzeigen_date)");

        // Bind parameters
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR); // Using PARAM_STR for DECIMAL
        $stmt->bindParam(':image', $data['image'], PDO::PARAM_STR);
        $stmt->bindParam(':kleinanzeigen_state', $data['kleinanzeigen_state'], PDO::PARAM_STR);
        $stmt->bindParam(':kleinanzeigen_date', $data['kleinanzeigen_date'], PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        $insertId = $pdo->lastInsertId();
        $success_message = 'Item inserted successfully with ID ' . $insertId;
        write_log($success_message);
        echo json_encode(['success' => true, 'message' => $success_message]);
    } catch (PDOException $e) {
        http_response_code(500);
        $error_message = 'Database error: ' . $e->getMessage();
        write_log($error_message);
        echo json_encode(['error' => $error_message]);
    } catch (Exception $e) {
        http_response_code(500);
        $error_message = 'General error: ' . $e->getMessage();
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