<?php
// delete_item.php
// flori

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