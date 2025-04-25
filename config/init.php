<?php
// Initialize the application
session_start();

// Define constants
define('ROOT_PATH', dirname(__DIR__) . '/');
define('URL_ROOT', 'http://localhost/finance/');

// Include database and other core files
require_once ROOT_PATH . 'config/database.php';

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if database tables exist
function checkDatabaseTables() {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) return false;
    
    try {
        // Check if journal_details table exists
        $result = $db->query("SHOW TABLES LIKE 'journal_details'");
        if ($result->rowCount() == 0) {
            // Table doesn't exist, redirect to setup
            header('Location: ' . URL_ROOT . 'setup.php?missing_tables=1');
            exit;
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Call the function if we're not already on the setup page
$currentPage = basename($_SERVER['SCRIPT_NAME']);
if ($currentPage !== 'setup.php') {
    checkDatabaseTables();
}

// Function to autoload classes
spl_autoload_register(function($className) {
    // Convert namespace to file path
    $folders = ['models', 'controllers', 'helpers'];
    
    foreach($folders as $folder) {
        $file = ROOT_PATH . $folder . '/' . $className . '.php';
        if(file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // If we reach here, the class wasn't found
    error_log("Class not found: $className");
});

// Function to check if a view file exists before including it
function safeInclude($filePath) {
    if(file_exists($filePath)) {
        include_once $filePath;
        return true;
    } else {
        error_log("View file not found: $filePath");
        include_once ROOT_PATH . 'views/errors/missing_view.php';
        return false;
    }
}
?>
