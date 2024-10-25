<?php
// add_item_ajax.php

header('Content-Type: application/json');

// Database connection parameters
$servername = "localhost";
$username = "your_username";     // Replace with your MySQL username
$password = "your_password";     // Replace with your MySQL password
$dbname = "shop_db";             // Ensure this matches your database name

$response = array('success' => false, 'message' => '');

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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