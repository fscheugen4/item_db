// ==UserScript==
// @name         Kleinanzeigen Import to floris_shop_db
// @namespace    http://tampermonkey.net/
// @version      1.4
// @description  Adds a button to import items to floris_shop_db on ad editing page
// @author       Flori
// @match        https://www.kleinanzeigen.de/*
// @grant        GM_xmlhttpRequest
// @connect      yourserver.com
// @connect      localhost
// @connect      localhost:8080
// ==/UserScript==

(function() {
    'use strict';

    // Wait for the page to fully load
    window.addEventListener('load', function() {
        // Check if we're on the ad editing page
        if (window.location.pathname.startsWith('/p-anzeige-bearbeiten.html')) {
            addImportButton();
        }
    });

    function addImportButton() {
        // Create the button
        const importButton = document.createElement('button');
        importButton.textContent = 'Import to floris_shop_db';
        importButton.style.position = 'fixed';
        importButton.style.top = '10px';
        importButton.style.right = '10px';
        importButton.style.zIndex = '1000';
        importButton.style.padding = '10px';
        importButton.style.backgroundColor = '#28a745';
        importButton.style.color = '#fff';
        importButton.style.border = 'none';
        importButton.style.borderRadius = '5px';
        importButton.style.cursor = 'pointer';

        // Append the button to the body
        document.body.appendChild(importButton);

        // Add click event listener
        importButton.addEventListener('click', function() {
            // Extract item data from form fields
            const itemNameInput = document.querySelector('input[name="postAdForm.title"]');
            const itemDescriptionTextarea = document.querySelector('textarea[name="postAdForm.description"]');
            const itemPriceInput = document.querySelector('input[name="postAdForm.price"]');
            const itemImageInput = document.querySelector('input[name="postAdForm.images"]'); // Adjust selector as needed
            const itemStateSelect = document.querySelector('select[name="postAdForm.condition"]');
            const adDateElement = document.querySelector('input[name="postAdForm.startDate"]'); // Adjust selector as needed

            if (itemNameInput && itemDescriptionTextarea && itemPriceInput) {
                const itemName = itemNameInput.value.trim();
                const itemDescription = itemDescriptionTextarea.value.trim();
                const itemPrice = parseFloat(itemPriceInput.value.replace(',', '.')) || 0.00;
                const kleinanzeigenState = itemStateSelect ? itemStateSelect.value : '';
                const kleinanzeigenDate = adDateElement ? adDateElement.value : '';

                // Extract image data
                let itemImage = '';
                if (itemImageInput && itemImageInput.files && itemImageInput.files[0]) {
                    const file = itemImageInput.files[0];
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        itemImage = e.target.result;

                        // After image data is read, proceed to send data
                        sendDataToServer({
                            name: itemName,
                            description: itemDescription,
                            price: itemPrice.toFixed(2),
                            image: itemImage,
                            kleinanzeigen_state: kleinanzeigenState,
                            kleinanzeigen_date: kleinanzeigenDate
                        });
                    };
                    reader.readAsDataURL(file);
                } else {
                    // If no image selected, proceed without image data
                    sendDataToServer({
                        name: itemName,
                        description: itemDescription,
                        price: itemPrice.toFixed(2),
                        image: '',
                        kleinanzeigen_state: kleinanzeigenState,
                        kleinanzeigen_date: kleinanzeigenDate
                    });
                }
            } else {
                alert('Unable to extract item data. Please ensure you are on the correct page.');
            }
        });
    }

    function sendDataToServer(data) {
        GM_xmlhttpRequest({
            method: 'POST',
            url: 'https://experten.bottomoftheinternet.com/flo/endpoint.php', // Replace with your actual endpoint
            headers: {
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(data),
            onload: function(response) {
                try {
                    const serverResponse = JSON.parse(response.responseText);
                    if (response.status === 200 && serverResponse.success) {
                        alert('Item imported successfully: ' + serverResponse.message);
                    } else {
                        alert('Server error: ' + serverResponse.error);
                    }
                } catch (e) {
                    alert('Failed to parse server response: ' + e.message);
                }
            },
            onerror: function() {
                alert('An error occurred while sending data to the server.');
            }
        });
    }

})();