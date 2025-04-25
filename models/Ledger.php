<?php
class Ledger {
    private $conn;
    private $table = "accounts";
    
    public $id;
    public $account_name;
    public $account_type;
    public $balance;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get all accounts
    public function getAccounts() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY account_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get single account
    public function getSingleAccount() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set properties
        $this->account_name = $row['account_name'];
        $this->account_type = $row['account_type'];
        $this->balance = $row['balance'];
    }
    
    // Ensure account exists, create if it doesn't
    public function ensureAccountExists($account_name, $account_type = 'asset') {
        $query = "SELECT id FROM accounts WHERE account_name = :account_name LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_name', $account_name);
        $stmt->execute();
        
        if($stmt->rowCount() == 0) {
            // Account doesn't exist, create it
            $query = "INSERT INTO accounts (account_name, account_type, balance) 
                      VALUES (:name, :type, 0)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $account_name);
            $stmt->bindParam(':type', $account_type);
            $stmt->execute();
        }
    }
    
    // Update account balance
    public function updateBalance($account_name, $amount, $type) {
        $query = "UPDATE " . $this->table . " 
                 SET balance = balance " . ($type == 'debit' ? '+' : '-') . " :amount 
                 WHERE account_name = :account_name";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':account_name', $account_name);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
    
    // Update account balance
    public function updateAccountBalance($account_name, $amount, $type) {
        // First determine the account type to know how it should be updated
        $query = "SELECT account_type FROM accounts WHERE account_name = :account_name LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_name', $account_name);
        $stmt->execute();
        
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$account) {
            // Account doesn't exist, create it first
            $this->ensureAccountExists($account_name);
            $account = ['account_type' => 'asset']; // Default to asset
        }
        
        // Determine if the account normally increases with debits
        $is_debit_normal = in_array($account['account_type'], ['asset', 'expense']);
        
        // Calculate the adjustment based on account type and transaction type
        $adjustment = $amount;
        if (($is_debit_normal && $type === 'credit') || (!$is_debit_normal && $type === 'debit')) {
            $adjustment = -$amount; // Negative adjustment
        }
        
        // Update the account balance
        $query = "UPDATE accounts 
                 SET balance = balance + :adjustment 
                 WHERE account_name = :account_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':adjustment', $adjustment);
        $stmt->bindParam(':account_name', $account_name);
        
        return $stmt->execute();
    }
    
    // Get account ledger entries
    public function getAccountEntries($account_name) {
        $query = "SELECT j.date, j.description, 
                 CASE WHEN j.debit_account = :account_name THEN j.amount ELSE 0 END AS debit,
                 CASE WHEN j.credit_account = :account_name THEN j.amount ELSE 0 END AS credit
                 FROM journal_entries j
                 WHERE j.debit_account = :account_name OR j.credit_account = :account_name
                 ORDER BY j.date";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_name', $account_name);
        $stmt->execute();
        
        return $stmt;
    }
}
?>
