<?php
class Computation {
    private $conn;
    private $table = "computations";
    
    public function __construct($db) {
        $this->conn = $db;
        $this->ensureTableExists();
    }
    
    // Ensure the computations table exists
    private function ensureTableExists() {
        try {
            // Check if table exists
            $stmt = $this->conn->query("SHOW TABLES LIKE '{$this->table}'");
            if ($stmt->rowCount() == 0) {
                // Create the table if it doesn't exist
                $this->conn->exec("CREATE TABLE IF NOT EXISTS {$this->table} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    dr_value DECIMAL(15,2) DEFAULT 0.00,
                    cr_value DECIMAL(15,2) DEFAULT 0.00,
                    result DECIMAL(15,2) DEFAULT 0.00,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
            }
        } catch (PDOException $e) {
            // Log error but don't throw exception to prevent app crashing
            error_log("Error creating computations table: " . $e->getMessage());
        }
    }
    
    // Save computation
    public function save($dr_value, $cr_value, $result) {
        $query = "INSERT INTO " . $this->table . " 
                 (dr_value, cr_value, result) 
                 VALUES 
                 (:dr_value, :cr_value, :result)";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':dr_value', $dr_value);
        $stmt->bindParam(':cr_value', $cr_value);
        $stmt->bindParam(':result', $result);
        
        // Execute query
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    // Get all computations
    public function getAll() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Return empty array if table doesn't exist yet
            return [];
        }
    }
    
    // Delete computation
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Get computation count
    public function getCount() {
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (PDOException $e) {
            // Return 0 if table doesn't exist yet
            return 0;
        }
    }
}
?>
