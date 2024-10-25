// ==UserScript==
// @name         Kleinanzeigen Import to item_db
// @namespace    http://tampermonkey.net/
// @version      1.2
// @description  Adds a button to import items to item_db on ad editing page
// @author       Your Name
// @match        https://www.kleinanzeigen.de/*
// @grant        GM_xmlhttpRequest
// @connect      http://localhost:8080/item_db/item_db/endpoint.php'
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
        importButton.textContent = 'Import to item_db';
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
            // Extract item data from form fields using updated selectors
            const itemNameInput = document.querySelector('input#postad-title');
            const itemDescriptionTextarea = document.querySelector('textarea#pstad-descrptn');
            const itemPriceInput = document.querySelector('input#micro-frontend-price');

            if (itemNameInput && itemDescriptionTextarea && itemPriceInput) {
                const itemName = itemNameInput.value.trim();
                const itemDescription = itemDescriptionTextarea.value.trim();
                const itemPrice = parseFloat(itemPriceInput.value.replace(',', '.')) || 0.00;

                // Prepare data object
                const itemData = {
                    name: itemName,
                    description: itemDescription,
                    price: itemPrice.toFixed(2)
                };

                // Send data to your server
                sendDataToServer(itemData);
            } else {
                alert('Unable to extract item data. Please ensure you are on the correct page.');
            }
        });
    }

    function sendDataToServer(data) {
        GM_xmlhttpRequest({
            method: 'POST',
            url: 'http://localhost:8080/item_db/item_db/endpoint.php', // Replace with your actual endpoint
            headers: {
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(data),
            onload: function(response) {
                if (response.status === 200) {
                    alert('Item imported successfully!');
                } else {
                    alert('Failed to import item. Server responded with status: ' + response.status);
                }
            },
            onerror: function() {
                alert('An error occurred while sending data to the server.');
            }
        });
    }

})();