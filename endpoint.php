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

// Log file path
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

        // Begin transaction
        $pdo->beginTransaction();

        // Insert into floris_shop_db table
        $stmt = $pdo->prepare("INSERT INTO floris_shop_db (name, description, price, kleinanzeigen_state, kleinanzeigen_date) VALUES (:name, :description, :price, :kleinanzeigen_state, :kleinanzeigen_date)");
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':kleinanzeigen_state' => $data['kleinanzeigen_state'],
            ':kleinanzeigen_date' => $data['kleinanzeigen_date'],
        ]);

        $itemId = $pdo->lastInsertId();

        // Insert images into item_images table
        if (!empty($data['images']) && is_array($data['images'])) {
            $imageStmt = $pdo->prepare("INSERT INTO item_images (item_id, image) VALUES (:item_id, :image)");
            foreach ($data['images'] as $imageData) {
                $imageStmt->execute([
                    ':item_id' => $itemId,
                    ':image' => $imageData,
                ]);
            }
        }

        // Commit transaction
        $pdo->commit();

        $success_message = 'Item inserted successfully with ID ' . $itemId;
        write_log($success_message);
        echo json_encode(['success' => true, 'message' => $success_message]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        $error_message = 'Database error: ' . $e->getMessage();
        write_log($error_message);
        echo json_encode(['error' => $error_message]);
    } catch (Exception $e) {
        $pdo->rollBack();
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