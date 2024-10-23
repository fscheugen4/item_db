<?php
// add_item.php
// flori

// Database connection parameters
$servername = "localhost";
$username = "item_db";     // Replace with your MySQL username
$password = "haxx0r";     // Replace with your MySQL password
$dbname = "item_db";             // Ensure this matches your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data and sanitize inputs
$name = $conn->real_escape_string($_POST['name']);
$description = $conn->real_escape_string($_POST['description']);
$price = (float) $_POST['price'];

// Prepare and execute the SQL statement
$sql = "INSERT INTO items (name, description, price) VALUES ('$name', '$description', $price)";

if ($conn->query($sql) === TRUE) {
    echo "New item added successfully. <a href='index.html'>Add another item</a> | <a href='view_items.php'>View Items</a>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>