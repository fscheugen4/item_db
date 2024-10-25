// ==UserScript==
// @name         Kleinanzeigen Import to item_db
// @namespace    http://tampermonkey.net/
// @version      1.0
// @description  Adds a button to import items to item_db
// @author       Your Name
// @match        https://www.kleinanzeigen.de/*
// @grant        GM_xmlhttpRequest
// @connect      http://localhost:8080/item_db/item_db/endpoint.php
// ==/UserScript==

(function() {
    'use strict';

    // Wait for the page to fully load
    window.addEventListener('load', function() {
        // Check if we're on an item page by looking for a unique element
        if (document.querySelector('[data-testid="page-title"]')) {
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
            // Extract item data
            const itemName = document.querySelector('[data-testid="page-title"]').innerText.trim();
            const itemDescription = document.querySelector('[data-testid="description"]').innerText.trim();
            const itemPriceElement = document.querySelector('[data-testid="advert-price"]');
            let itemPrice = 0.00;
            if (itemPriceElement) {
                const priceText = itemPriceElement.innerText.trim();
                // Extract numerical value from the price text
                itemPrice = parseFloat(priceText.replace(/[^\d,.-]/g, '').replace(',', '.')) || 0.00;
            }

            // Prepare data object
            const itemData = {
                name: itemName,
                description: itemDescription,
                price: itemPrice.toFixed(2)
            };

            // Send data to your server
            sendDataToServer(itemData);
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