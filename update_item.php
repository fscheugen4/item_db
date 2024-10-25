<?php
// update_item.php

header('Content-Type: application/json');

// Database connection parameters
$servername = "localhost";
$username = "item_db";     // Replace with your MySQL username
$password = "haxx0r";     // Replace with your MySQL password
$dbname = "shop_db";             // Ensure this matches your database name

$response = array('success' => false, 'message' => '');

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);

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