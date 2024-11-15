<?php
// add_item_ajax.php

header('Content-Type: application/json');
$response = array('success' => false, 'message' => '');

// Include the database configuration
require_once 'config.php';

try {
    // Get form data and sanitize inputs
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';

    // Validate input data
    if (empty($name) || empty($price)) {
        throw new Exception('Name and price are required.');
    }

    // Prepare and execute the SQL statement using prepared statements
    $stmt = $conn->prepare("INSERT INTO items (name, description, price) VALUES (:name, :description, :price)");
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':price' => $price
    ]);

    $response['success'] = true;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return the JSON response
echo json_encode($response);
?>