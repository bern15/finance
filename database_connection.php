<?php
// Database connection script
$host = 'localhost';
$db_name = 'finance';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection established successfully";
} catch(PDOException $e) {
    echo "Connection Error: " . $e->getMessage();
}
?>
