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
    $kleinanzeigen_state = $_POST['kleinanzeigen_state'] ?? '';
    $kleinanzeigen_date = $_POST['kleinanzeigen_date'] ?? '';

    if (empty($id) || empty($name) || empty($price)) {
        throw new Exception('ID, name, and price are required.');
    }

    // Handle the image if provided
    $imageData = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Get the image file content
        $imageFile = $_FILES['image']['tmp_name'];
        $imageContent = file_get_contents($imageFile);
        // Encode the image to base64
        $imageData = base64_encode($imageContent);
    }

    // Prepare the SQL statement
    $sql = "UPDATE floris_shop_db SET name = :name, description = :description, price = :price, kleinanzeigen_state = :kleinanzeigen_state, kleinanzeigen_date = :kleinanzeigen_date";
    if ($imageData !== null) {
        $sql .= ", image = :image";
    }
    $sql .= " WHERE id = :id";

    $stmt = $conn->prepare($sql);

    // Bind parameters
    $params = [
        ':id' => $id,
        ':name' => $name,
        ':description' => $description,
        ':price' => $price,
        ':kleinanzeigen_state' => $kleinanzeigen_state,
        ':kleinanzeigen_date' => $kleinanzeigen_date
    ];
    if ($imageData !== null) {
        $params[':image'] = $imageData;
    }

    $stmt->execute($params);

    $response['success'] = true;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>