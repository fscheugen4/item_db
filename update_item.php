<?php
// update_item.php

header('Content-Type: application/json');
$response = array('success' => false, 'message' => '');

// Include the database configuration
require_once 'config.php';

try {
    // Get and sanitize POST data
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';

    if (empty($id) || empty($name) || empty($price)) {
        throw new Exception('ID, name, and price are required.');
    }

    // Update the item
    $stmt = $conn->prepare("UPDATE items SET name = :name, description = :description, price = :price WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':description' => $description,
        ':price' => $price
    ]);

    $response['success'] = true;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>