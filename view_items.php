<?php
// view_items.php

// No need to set database parameters here
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
        var state = row.find('.kleinanzeigen_state').text();
        var date = row.find('.kleinanzeigen_date').text();

        // Create editable fields
        row.find('.name').html('<input type="text" value="'+name+'">');
        row.find('.description').html('<textarea>'+description+'</textarea>');
        row.find('.price').html('<input type="number" step="0.01" value="'+price+'">');
        row.find('.kleinanzeigen_state').html('<input type="text" value="'+state+'">');
        row.find('.kleinanzeigen_date').html('<input type="date" value="'+date+'">');

        // Display existing images with delete option
        var imagesCell = row.find('td:eq(4)');
        var imagesHtml = '';
        imagesCell.find('img').each(function() {
            var imgSrc = $(this).attr('src');
            var imageId = $(this).data('image-id');
            imagesHtml += '<div class="image-wrapper">';
            imagesHtml += '<img src="'+imgSrc+'" data-image-id="'+imageId+'" width="100">';
            imagesHtml += '<button class="delete-image-btn" data-image-id="'+imageId+'">Delete</button>';
            imagesHtml += '</div>';
        });

        imagesHtml += '<input type="file" class="edit-images" name="images[]" accept="image/*" multiple>';
        imagesCell.html(imagesHtml);

        // Change buttons
        $(this).hide();
        row.find('.delete-btn').hide();
        row.find('.save-btn').show();
        row.find('.cancel-btn').show();
    });

    // Handle image deletion
    var imagesToDelete = [];
    $(document).on('click', '.delete-image-btn', function() {
        var imageId = $(this).data('image-id');
        imagesToDelete.push(imageId);
        $(this).closest('.image-wrapper').remove();
    });

    // Cancel edit
    $(document).on('click', '.cancel-btn', function() {
        imagesToDelete = [];
        loadItems();
    });

    // Save edited item
    $(document).on('click', '.save-btn', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        var name = row.find('.name input').val();
        var description = row.find('.description textarea').val();
        var price = row.find('.price input').val();
        var state = row.find('.kleinanzeigen_state input').val();
        var date = row.find('.kleinanzeigen_date input').val();
        var imageFiles = row.find('.edit-images')[0].files;

        var formData = new FormData();
        formData.append('id', id);
        formData.append('name', name);
        formData.append('description', description);
        formData.append('price', price);
        formData.append('kleinanzeigen_state', state);
        formData.append('kleinanzeigen_date', date);

        // Append new images
        for (var i = 0; i < imageFiles.length; i++) {
            formData.append('images[]', imageFiles[i]);
        }

        // Append images to delete
        if (imagesToDelete.length > 0) {
            formData.append('delete_image_ids', imagesToDelete.join(','));
        }

        $.ajax({
            url: 'update_item.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            encode: true,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.success) {
                    alert('Item updated successfully.');
                    imagesToDelete = [];
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