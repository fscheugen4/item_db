-- Create the database
CREATE DATABASE IF NOT EXISTS shop_db;

-- Use the database
USE shop_db;

-- Create the items table
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL
);