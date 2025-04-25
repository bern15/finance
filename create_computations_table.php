<?php
// Script to create the missing computations table

require_once 'config/init.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

if(!$db) {
    die("Database connection failed. Please check your configuration.");
}

try {
    echo "<h2>Creating Computations Table</h2>";
    
    // Create the computations table
    $db->exec("CREATE TABLE IF NOT EXISTS computations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dr_value DECIMAL(15,2) DEFAULT 0.00,
        cr_value DECIMAL(15,2) DEFAULT 0.00,
        result DECIMAL(15,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    echo "<p>Computations table created successfully!</p>";
    echo "<p><a href='index.php' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;'>Return to Dashboard</a></p>";
    
} catch(PDOException $e) {
    echo "<h2>Error Creating Table</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p><a href='index.php'>Return to Dashboard</a></p>";
}
?>
