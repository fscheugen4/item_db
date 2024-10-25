<?php
// view_items.php
// flori

// Database connection parameters
$servername = "localhost";
$username = "item_db";     // Replace with your MySQL username
$password = "haxx0r";     // Replace with your MySQL password
$dbname = "shop_db";             // Ensure this matches your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch items from the database
$sql = "SELECT id, name, description, price FROM items";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Items for Sale</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Items for Sale</h1>
    <a href="index.html">Add New Item</a><br><br>
    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price ($)</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No items found.</p>
    <?php endif; ?>
    <?php $conn->close(); ?>
</body>
</html>