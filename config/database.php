<?php
class Database {
    private $host = "localhost";
    private $db_name = "finance_db";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            // First connect without specifying a database
            $this->conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check if database exists, create if it doesn't
            $this->createDatabaseIfNotExists();
            
            // Connect to the specific database
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
            $this->conn = null;
        }
        
        return $this->conn;
    }
    
    private function createDatabaseIfNotExists() {
        try {
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);
        } catch(PDOException $e) {
            echo "Error creating database: " . $e->getMessage();
        }
    }
    
    public function tablesExist() {
        if(!$this->conn) {
            return false;
        }
        
        try {
            // Check if at least one table exists
            $stmt = $this->conn->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // If we have any tables at all, consider it valid
            return count($tables) > 0;
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>
