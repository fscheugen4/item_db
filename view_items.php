<?php
// view_items.php

// Database connection parameters
$servername = "localhost";
$username = "item_db";     // Replace with your MySQL username
$password = "haxx0r";     // Replace with your MySQL password
$dbname = "shop_db";             // Ensure this matches your database name

?>
<!DOCTYPE html>
<html>
<head>
    <title>Items for Sale</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Items for Sale</h1>
    <a href="index.html">Add New Item</a><br><br>

    <!-- Items Table -->
    <div id="itemsTable">
        <!-- Items will be loaded here -->
    </div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Add the script for loading and handling items -->
    <script>
    $(document).ready(function() {
        loadItems();

        // Function to load items
        function loadItems() {
            $.ajax({
                url: 'get_items.php',
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $('#itemsTable').html(data);
                },
                error: function() {
                    alert('Failed to load items.');
                }
            });
        }

        // Edit item
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var row = $(this).closest('tr');
            var name = row.find('.name').text();
            var description = row.find('.description').text();
            var price = row.find('.price').text();

            // Create editable fields
            row.find('.name').html('<input type="text" value="'+name+'">');
            row.find('.description').html('<textarea>'+description+'</textarea>');
            row.find('.price').html('<input type="number" step="0.01" value="'+price+'">');

            // Change buttons
            $(this).hide();
            row.find('.delete-btn').hide();
            row.find('.save-btn').show();
            row.find('.cancel-btn').show();
        });

        // Cancel edit
        $(document).on('click', '.cancel-btn', function() {
            loadItems();
        });

        // Save edited item
        $(document).on('click', '.save-btn', function() {
            var id = $(this).data('id');
            var row = $(this).closest('tr');
            var name = row.find('.name input').val();
            var description = row.find('.description textarea').val();
            var price = row.find('.price input').val();

            $.ajax({
                url: 'update_item.php',
                type: 'POST',
                data: {
                    id: id,
                    name: name,
                    description: description,
                    price: price
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        alert('Item updated successfully.');
                        loadItems();
                    } else {
                        alert('Error: ' + data.message);
                    }
                },
                error: function() {
                    alert('Failed to update item.');
                }
            });
        });

        // Delete item
        $(document).on('click', '.delete-btn', function() {
            if (!confirm('Are you sure you want to delete this item?')) {
                return;
            }

            var id = $(this).data('id');

            $.ajax({
                url: 'delete_item.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        alert('Item deleted successfully.');
                        loadItems();
                    } else {
                        alert('Error: ' + data.message);
                    }
                },
                error: function() {
                    alert('Failed to delete item.');
                }
            });
        });
    });
    </script>
</body>
</html>