-- Create the database
CREATE DATABASE IF NOT EXISTS dbs13497512;

-- Use the database
USE dbs13497512;

-- Create the items table (if not already existing)
CREATE TABLE IF NOT EXISTS floris_shop_db (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    kleinanzeigen_state VARCHAR(255),
    kleinanzeigen_date DATE
);

-- Create the item_images table
CREATE TABLE IF NOT EXISTS floris_item_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    image MEDIUMTEXT NOT NULL,
    FOREIGN KEY (item_id) REFERENCES floris_shop_db(id) ON DELETE CASCADE
);