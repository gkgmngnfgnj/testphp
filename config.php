<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'guns_lol_clone');
define('DB_USER', 'root');
define('DB_PASS', '');

// Website configuration
define('SITE_URL', 'http://localhost/guns-lol-clone');
define('SITE_NAME', 'Guns.lol Clone');

// Create database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If database doesn't exist, create it
    if ($e->getCode() == 1049) {
        $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $pdo->exec("CREATE DATABASE " . DB_NAME);
        $pdo->exec("USE " . DB_NAME);
        createTables($pdo);
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}

// Start session
session_start();

// Function to create tables
function createTables($pdo) {
    $sql = "
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        display_name VARCHAR(100) NOT NULL,
        email VARCHAR(255),
        password VARCHAR(255),
        avatar_url VARCHAR(500),
        bio TEXT,
        custom_html TEXT,
        view_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE user_links (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        title VARCHAR(100),
        url VARCHAR(500),
        display_order INT DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    INSERT INTO users (username, display_name, bio) VALUES 
    ('john', 'John Doe', 'Gaming enthusiast and content creator'),
    ('sarah', 'Sarah Smith', 'Professional gamer and streamer'),
    ('demo', 'Demo User', 'Check out my awesome profile!');
    ";
    
    $pdo->exec($sql);
}
?>