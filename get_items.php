<?php
// get_items.php

// Include the database configuration
require_once 'config.php';

// Fetch items from the database
try {
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
} catch (Exception $e) {
    echo '<p>Error fetching items: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>