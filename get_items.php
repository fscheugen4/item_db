<?php
// get_items.php

// Database connection parameters
$servername = "localhost";
$username = "your_username";     // Replace with your MySQL username
$password = "your_password";     // Replace with your MySQL password
$dbname = "shop_db";             // Ensure this matches your database name

// Create a new PDO connection
$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);

// Fetch items from the database
$sql = "SELECT id, name, description, price FROM items";
$stmt = $conn->prepare($sql);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate the HTML table
if (count($items) > 0) {
    echo '<table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price ($)</th>
                <th>Actions</th>
            </tr>';
    foreach ($items as $row) {
        echo '<tr>
                <td>'.htmlspecialchars($row['id']).'</td>
                <td class="name">'.htmlspecialchars($row['name']).'</td>
                <td class="description">'.nl2br(htmlspecialchars($row['description'])).'</td>
                <td class="price">'.number_format($row['price'], 2).'</td>
                <td>
                    <button class="edit-btn" data-id="'.htmlspecialchars($row['id']).'">Edit</button>
                    <button class="save-btn" data-id="'.htmlspecialchars($row['id']).'" style="display:none;">Save</button>
                    <button class="cancel-btn" data-id="'.htmlspecialchars($row['id']).'" style="display:none;">Cancel</button>
                    <button class="delete-btn" data-id="'.htmlspecialchars($row['id']).'">Delete</button>
                </td>
              </tr>';
    }
    echo '</table>';
} else {
    echo '<p>No items found.</p>';
}
?>