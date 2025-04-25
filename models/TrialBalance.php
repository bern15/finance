<?php
class TrialBalance {
    private $conn;
    private $table = "trial_balance_entries";
    
    public function __construct($db) {
        $this->conn = $db;
        $this->ensureTableExists();
    }
    
    // Ensure the trial balance table exists
    private function ensureTableExists() {
        try {
            // Check if table exists
            $stmt = $this->conn->query("SHOW TABLES LIKE '{$this->table}'");
            if ($stmt->rowCount() == 0) {
                // Create the table if it doesn't exist
                $this->conn->exec("CREATE TABLE IF NOT EXISTS {$this->table} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    account_name VARCHAR(100) NOT NULL,
                    trial_balance_debit DECIMAL(15,2) DEFAULT 0.00,
                    trial_balance_credit DECIMAL(15,2) DEFAULT 0.00,
                    adjustment_debit DECIMAL(15,2) DEFAULT 0.00,
                    adjustment_credit DECIMAL(15,2) DEFAULT 0.00,
                    adjusted_balance_debit DECIMAL(15,2) DEFAULT 0.00,
                    adjusted_balance_credit DECIMAL(15,2) DEFAULT 0.00,
                    income_statement_debit DECIMAL(15,2) DEFAULT 0.00,
                    income_statement_credit DECIMAL(15,2) DEFAULT 0.00,
                    balance_sheet_debit DECIMAL(15,2) DEFAULT 0.00,
                    balance_sheet_credit DECIMAL(15,2) DEFAULT 0.00,
                    user_id INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )");
            } else {
                // Check if user_id column exists and add it if it doesn't
                try {
                    $stmt = $this->conn->query("SHOW COLUMNS FROM {$this->table} LIKE 'user_id'");
                    if ($stmt->rowCount() == 0) {
                        $this->conn->exec("ALTER TABLE {$this->table} ADD COLUMN user_id INT AFTER balance_sheet_credit");
                        $this->conn->exec("ALTER TABLE {$this->table} ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
                    }
                } catch (PDOException $e) {
                    error_log("Error checking/adding user_id column: " . $e->getMessage());
                }
            }
        } catch (PDOException $e) {
            error_log("Error creating trial_balance_entries table: " . $e->getMessage());
        }
    }
    
    // Save trial balance data with user association
    public function saveTrialBalance($data, $userId) {
        try {
            // Begin transaction explicitly
            $this->conn->beginTransaction();
            
            // Clear existing data for this user
            $deleteQuery = "DELETE FROM {$this->table} WHERE user_id = :user_id";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bindParam(':user_id', $userId);
            $deleteStmt->execute();
            
            // Prepare insertion statement
            $query = "INSERT INTO {$this->table} (
                        account_name, 
                        trial_balance_debit, 
                        trial_balance_credit, 
                        adjustment_debit, 
                        adjustment_credit, 
                        adjusted_balance_debit, 
                        adjusted_balance_credit, 
                        income_statement_debit, 
                        income_statement_credit, 
                        balance_sheet_debit, 
                        balance_sheet_credit,
                        user_id
                      ) VALUES (
                        :account_name,
                        :trial_balance_debit,
                        :trial_balance_credit,
                        :adjustment_debit,
                        :adjustment_credit,
                        :adjusted_balance_debit,
                        :adjusted_balance_credit,
                        :income_statement_debit,
                        :income_statement_credit,
                        :balance_sheet_debit,
                        :balance_sheet_credit,
                        :user_id
                      )";
            
            // Prepare the statement
            $stmt = $this->conn->prepare($query);
            
            // Insert each row
            foreach ($data as $row) {
                $stmt->bindParam(':account_name', $row['account_name']);
                $stmt->bindParam(':trial_balance_debit', $row['trial_balance_debit']);
                $stmt->bindParam(':trial_balance_credit', $row['trial_balance_credit']);
                $stmt->bindParam(':adjustment_debit', $row['adjustment_debit']);
                $stmt->bindParam(':adjustment_credit', $row['adjustment_credit']);
                $stmt->bindParam(':adjusted_balance_debit', $row['adjusted_balance_debit']);
                $stmt->bindParam(':adjusted_balance_credit', $row['adjusted_balance_credit']);
                $stmt->bindParam(':income_statement_debit', $row['income_statement_debit']);
                $stmt->bindParam(':income_statement_credit', $row['income_statement_credit']);
                $stmt->bindParam(':balance_sheet_debit', $row['balance_sheet_debit']);
                $stmt->bindParam(':balance_sheet_credit', $row['balance_sheet_credit']);
                $stmt->bindParam(':user_id', $userId);
                
                $stmt->execute();
            }
            
            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            // Rollback the transaction on error
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error saving trial balance: " . $e->getMessage());
            return false;
        }
    }
    
    // Get trial balance entries for a specific user
    public function getByUserId($userId) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY id ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching trial balance entries: " . $e->getMessage());
            return [];
        }
    }
    
    // Get all trial balance entries
    public function getAllEntries() {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY id ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching trial balance entries: " . $e->getMessage());
            return [];
        }
    }
}
?>
