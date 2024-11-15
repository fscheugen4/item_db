<?php
// delete_item.php

header('Content-Type: application/json');
$response = array('success' => false, 'message' => '');

// Include the database configuration
require_once 'config.php';

try {
    // Get and sanitize POST data
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        throw new Exception('ID is required.');
    }

    // Delete the item
    $stmt = $conn->prepare("DELETE FROM items WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $response['success'] = true;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>