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

    // Begin transaction
    $conn->beginTransaction();

    // Insert the item into floris_shop_db
    $stmt = $conn->prepare("INSERT INTO floris_shop_db (name, description, price, kleinanzeigen_state, kleinanzeigen_date) VALUES (:name, :description, :price, :kleinanzeigen_state, :kleinanzeigen_date)");
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':price' => $price,
        ':kleinanzeigen_state' => $kleinanzeigen_state,
        ':kleinanzeigen_date' => $kleinanzeigen_date
    ]);

    // Get the last inserted item ID
    $itemId = $conn->lastInsertId();

    // Handle multiple image uploads
    if (isset($_FILES['images'])) {
        $images = $_FILES['images'];
        for ($i = 0; $i < count($images['name']); $i++) {
            if ($images['error'][$i] === UPLOAD_ERR_OK) {
                $imageFile = $images['tmp_name'][$i];
                $imageContent = file_get_contents($imageFile);
                $imageData = base64_encode($imageContent);

                // Insert image into floris_item_images
                $stmt = $conn->prepare("INSERT INTO floris_item_images (item_id, image) VALUES (:item_id, :image)");
                $stmt->execute([
                    ':item_id' => $itemId,
                    ':image' => $imageData
                ]);
            } else {
                // Handle file upload error if necessary
                throw new Exception('Error uploading image: ' . $images['name'][$i]);
            }
        }
    }

    // Commit transaction
    $conn->commit();

    $response['success'] = true;
} catch (Exception $e) {
    // Rollback transaction in case of error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>