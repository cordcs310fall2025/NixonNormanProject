<?php
// db_config.php - Database configuration for Nixon Norman Media

// Database configuration for XAMPP
// When you set up your database in phpMyAdmin, update these values accordingly
// This file should be used for every php page in this website

$db_config = [
    'host' => 'localhost',
    'username' => 'root',          // Default XAMPP MySQL username
    'password' => '',              // Default XAMPP MySQL password (empty)
    'database' => 'nixon_norman_media',  // You'll create this database in phpMyAdmin
    'charset' => 'utf8mb4'
];

// Function to create database connection
function getDatabaseConnection() {
    global $db_config;
    
    try {
        $dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};charset={$db_config['charset']}";
        $pdo = new PDO($dsn, $db_config['username'], $db_config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        // For development, show error. In production, log it instead
        die("Database connection failed: " . $e->getMessage());
    }
}

// Function to check if database connection is working
function testDatabaseConnection() {
    try {
        $pdo = getDatabaseConnection();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// SQL to create the about_content table (run this in phpMyAdmin)
$about_content_table_sql = "
CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    headline VARCHAR(255) NOT NULL DEFAULT 'About Nixon Norman Media',
    intro TEXT,
    bio TEXT,
    mission TEXT,
    experience_years VARCHAR(10) DEFAULT '5+',
    projects_completed VARCHAR(10) DEFAULT '150+',
    happy_clients VARCHAR(10) DEFAULT '50+',
    services JSON,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
";

// SQL to insert default content
$about_content_default_data = "
INSERT INTO about_content (headline, intro, bio, mission, services) VALUES (
    'About Nixon Norman Media',
    'Capturing moments that matter through the lens of creativity and passion.',
    'Nixon Norman Media specializes in professional photography and videography services. With years of experience in automotive, commercial, and event photography, we bring your vision to life with stunning visual storytelling.',
    'Our mission is to deliver exceptional visual content that exceeds expectations and creates lasting impressions.',
    '[\"Commercial Photography\", \"Event Photography\", \"Automotive Photography\", \"Video Production\", \"Social Media Content\"]'
);
";

?>
