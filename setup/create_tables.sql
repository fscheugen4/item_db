-- Create the database
CREATE DATABASE IF NOT EXISTS floris_shop_db;

-- Use the database
USE floris_shop_db;

-- Create the items table
CREATE TABLE IF NOT EXISTS floris_shop_db (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL
);