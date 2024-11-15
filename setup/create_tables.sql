-- Create the database
CREATE DATABASE IF NOT EXISTS dbs13497512;

-- Use the database
USE dbs13497512;

-- Create the items table
CREATE TABLE IF NOT EXISTS floris_shop_db (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image MEDIUMTEXT,
    kleinanzeigen_state VARCHAR(255),
    kleinanzeigen_date DATE
);