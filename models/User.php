<?php
class User {
    private $conn;
    private $table = "users";
    
    public $id;
    public $username;
    public $password;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->ensureTablesExist();
    }
    
    // Ensure the users and user_activity tables exist
    private function ensureTablesExist() {
        try {
            // Check if users table exists
            $stmt = $this->conn->query("SHOW TABLES LIKE 'users'");
            if ($stmt->rowCount() == 0) {
                // Create users table
                $this->conn->exec("CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
            }
            
            // Check if user_activity table exists
            $stmt = $this->conn->query("SHOW TABLES LIKE 'user_activity'");
            if ($stmt->rowCount() == 0) {
                // Create user_activity table with ip_address column
                $this->conn->exec("CREATE TABLE IF NOT EXISTS user_activity (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT,
                    username VARCHAR(50),
                    activity_type VARCHAR(50),
                    ip_address VARCHAR(45),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                )");
            } else {
                // Check if ip_address column exists and add it if it doesn't
                try {
                    $stmt = $this->conn->query("SHOW COLUMNS FROM user_activity LIKE 'ip_address'");
                    if ($stmt->rowCount() == 0) {
                        $this->conn->exec("ALTER TABLE user_activity ADD COLUMN ip_address VARCHAR(45) AFTER activity_type");
                    }
                } catch (PDOException $e) {
                    error_log("Error checking/adding ip_address column: " . $e->getMessage());
                }
            }
        } catch (PDOException $e) {
            error_log("Error creating tables: " . $e->getMessage());
        }
    }
    
    // Register a new user
    public function register() {
        // Check if username already exists
        if($this->usernameExists()) {
            return false;
        }
        
        // Create query
        $query = "INSERT INTO " . $this->table . " 
                  (username, password) 
                  VALUES
                  (:username, :password)";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->username = htmlspecialchars(strip_tags($this->username));
        
        // Hash password
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Bind parameters
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        
        // Execute query
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Login a user
    public function login() {
        // Create query
        $query = "SELECT id, username, password FROM " . $this->table . " 
                  WHERE username = :username 
                  LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':username', $this->username);
        
        // Execute query
        $stmt->execute();
        
        // Get row count
        if($stmt->rowCount() == 0) {
            return false;
        }
        
        // Fetch user data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password
        if(password_verify($this->password, $row['password'])) {
            // Set properties
            $this->id = $row['id'];
            return true;
        }
        
        return false;
    }
    
    // Log user activity - modified to capture IP address
    public function logActivity($activityType) {
        // Check if we have a valid user ID
        if (empty($this->id) && isset($_SESSION['user_id'])) {
            $this->id = $_SESSION['user_id'];
        }
        
        // If username is not set but session username is available, use that
        if (empty($this->username) && isset($_SESSION['username'])) {
            $this->username = $_SESSION['username'];
        }
        
        // Get user's IP address
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        
        // Create the query - don't insert user_id if it's null
        if (!empty($this->id)) {
            $query = "INSERT INTO user_activity (user_id, username, activity_type, ip_address) 
                      VALUES (:user_id, :username, :activity_type, :ip_address)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $this->id);
        } else {
            // Insert without user_id to avoid foreign key constraints
            $query = "INSERT INTO user_activity (username, activity_type, ip_address) 
                      VALUES (:username, :activity_type, :ip_address)";
            
            $stmt = $this->conn->prepare($query);
        }
        
        // Bind remaining parameters
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':activity_type', $activityType);
        $stmt->bindParam(':ip_address', $ipAddress);
        
        return $stmt->execute();
    }
    
    // Get all users
    public function getAll() {
        $query = "SELECT id, username, created_at FROM " . $this->table . " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get all user activity
    public function getAllActivity() {
        $query = "SELECT ua.id, ua.user_id, ua.username, ua.activity_type, ua.ip_address, ua.created_at
                  FROM user_activity ua
                  ORDER BY ua.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get user's own activity (for user dashboard)
    public function getUserActivity($userId) {
        try {
            $query = "SELECT ua.id, ua.activity_type, ua.ip_address, ua.created_at
                      FROM user_activity ua
                      WHERE ua.user_id = :user_id
                      ORDER BY ua.created_at DESC
                      LIMIT 10";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Return empty array if error
            return [];
        }
    }
    
    // Check if username exists
    private function usernameExists() {
        // Create query
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE username = :username 
                  LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':username', $this->username);
        
        // Execute query
        $stmt->execute();
        
        // Return true if username exists
        return $stmt->rowCount() > 0;
    }
}
?>
