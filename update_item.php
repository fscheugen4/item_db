<?php
// update_item.php

header('Content-Type: application/json');
$response = array('success' => false, 'message' => '');

// Include the database configuration
require_once 'config.php';

try {
    // Begin transaction
    $conn->beginTransaction();

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

    // Update the item
    $stmt = $conn->prepare("UPDATE floris_shop_db SET name = :name, description = :description, price = :price, kleinanzeigen_state = :kleinanzeigen_state, kleinanzeigen_date = :kleinanzeigen_date WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':description' => $description,
        ':price' => $price,
        ':kleinanzeigen_state' => $kleinanzeigen_state,
        ':kleinanzeigen_date' => $kleinanzeigen_date
    ]);

    // Handle new images if provided
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
                    ':item_id' => $id,
                    ':image' => $imageData
                ]);
            } else {
                // Handle file upload error if necessary
                throw new Exception('Error uploading image: ' . $images['name'][$i]);
            }
        }
    }

    // Handle image deletions if any
    if (isset($_POST['delete_image_ids'])) {
        $deleteImageIds = explode(',', $_POST['delete_image_ids']);
        foreach ($deleteImageIds as $imageId) {
            $stmt = $conn->prepare("DELETE FROM floris_item_images WHERE id = :id AND item_id = :item_id");
            $stmt->execute([
                ':id' => $imageId,
                ':item_id' => $id
            ]);
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