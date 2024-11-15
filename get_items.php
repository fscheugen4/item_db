<?php
// get_items.php

// Include the database configuration
require_once 'config.php';

try {
    // Fetch items from the database
    $sql = "SELECT * FROM floris_shop_db";
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
                    <th>Images</th>
                    <th>State</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>';
        foreach ($items as $row) {
            echo '<tr>
                    <td>'.htmlspecialchars($row['id']).'</td>
                    <td class="name">'.htmlspecialchars($row['name']).'</td>
                    <td class="description">'.nl2br(htmlspecialchars($row['description'])).'</td>
                    <td class="price">'.number_format($row['price'], 2).'</td>';

            // Fetch images for this item
            $stmtImages = $conn->prepare("SELECT image FROM floris_item_images WHERE item_id = :item_id");
            $stmtImages->execute([':item_id' => $row['id']]);
            $images = $stmtImages->fetchAll(PDO::FETCH_ASSOC);

            // Display images
            echo '<td>';
            if (count($images) > 0) {
                foreach ($images as $image) {
                    echo '<img src="data:image/jpeg;base64,' . $image['image'] . '" alt="Image" width="100">';
                }
            } else {
                echo 'No Images';
            }
            echo '</td>';

            echo '<td class="kleinanzeigen_state">'.htmlspecialchars($row['kleinanzeigen_state']).'</td>';
            echo '<td class="kleinanzeigen_date">'.htmlspecialchars($row['kleinanzeigen_date']).'</td>';

            echo '<td>
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