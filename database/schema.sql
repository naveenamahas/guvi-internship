-- Run this file in phpMyAdmin (or mysql CLI) to create the database and table
-- used for storing REGISTERED USER DATA in MySQL.

CREATE DATABASE IF NOT EXISTS guvi_internship;
USE guvi_internship;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
