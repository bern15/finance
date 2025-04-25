<?php
class Journal {
    private $conn;
    private $table = "journal_entries";
    
    public $id;
    public $date;
    public $description;
    public $debit_account;
    public $credit_account;
    public $amount;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create journal entry
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 (date, description, debit_account, credit_account, amount, created_at) 
                 VALUES 
                 (:date, :description, :debit_account, :credit_account, :amount, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->debit_account = htmlspecialchars(strip_tags($this->debit_account));
        $this->credit_account = htmlspecialchars(strip_tags($this->credit_account));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        
        // Bind parameters
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':debit_account', $this->debit_account);
        $stmt->bindParam(':credit_account', $this->credit_account);
        $stmt->bindParam(':amount', $this->amount);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
    
    // Read all journal entries
    public function read() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read single journal entry
    public function readSingle() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set properties
        $this->date = $row['date'];
        $this->description = $row['description'];
        $this->debit_account = $row['debit_account'];
        $this->credit_account = $row['credit_account'];
        $this->amount = $row['amount'];
        $this->created_at = $row['created_at'];
    }
}
?>
