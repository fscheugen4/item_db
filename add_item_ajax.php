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
    $kleinanzeigen_state = $_POST['kleinanzeigen_state'] ?? '';
    $kleinanzeigen_date = $_POST['kleinanzeigen_date'] ?? '';

    // Validate input data
    if (empty($name) || empty($price)) {
        throw new Exception('Name and price are required.');
    }

    // Handle the uploaded image
    $imageData = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Get the image file content
        $imageFile = $_FILES['image']['tmp_name'];
        $imageContent = file_get_contents($imageFile);
        // Encode the image to base64
        $imageData = base64_encode($imageContent);
    }

    // Prepare and execute the SQL statement using prepared statements
    $stmt = $conn->prepare("INSERT INTO floris_shop_db (name, description, price, image, kleinanzeigen_state, kleinanzeigen_date) VALUES (:name, :description, :price, :image, :kleinanzeigen_state, :kleinanzeigen_date)");
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':price' => $price,
        ':image' => $imageData,
        ':kleinanzeigen_state' => $kleinanzeigen_state,
        ':kleinanzeigen_date' => $kleinanzeigen_date
    ]);

    $response['success'] = true;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return the JSON response
echo json_encode($response);
?>