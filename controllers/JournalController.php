<?php
class JournalController {
    private $db;
    private $journal;
    private $ledger;
    
    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Initialize models
        $this->journal = new Journal($this->db);
        $this->ledger = new Ledger($this->db);
    }
    
    // Display journal entries
    public function index() {
        try {
            // Check if required tables exist
            $this->checkTablesExist();
            
            // Get summarized journal entries
            $query = "SELECT je.id, je.journal_title, je.date 
                     FROM journal_entries je
                     ORDER BY je.date DESC, je.id DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Now fetch details for each entry separately to avoid duplication
            foreach ($entries as &$entry) {
                // Ensure we have a valid date
                if (empty($entry['date'])) {
                    $entry['date'] = date('Y-m-d');
                }
                
                $query = "SELECT jd.id, jd.account, jd.reference, jd.debit, jd.credit 
                         FROM journal_details jd 
                         WHERE jd.journal_id = :journal_id
                         ORDER BY jd.id ASC";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':journal_id', $entry['id']);
                $stmt->execute();
                
                $entry['details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // Check for success message
            $success_message = null;
            if (isset($_SESSION['success_message'])) {
                $success_message = $_SESSION['success_message'];
                unset($_SESSION['success_message']); // Clear the message
            }
            
            include_once ROOT_PATH . 'views/journal/index.php';
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Show create entry form
    public function create() {
        try {
            $accounts = $this->ledger->getAccounts();
            include_once ROOT_PATH . 'views/journal/create.php';
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Process form submission
    public function store() {
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URL_ROOT . 'index.php?page=journal');
            return;
        }
        
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Check if required tables exist
            $this->checkTablesExist();
            
            // Get date from the first entry, fallback to current date
            $firstDate = date('Y-m-d');  // Default to today
            
            if (isset($_POST['tables']) && is_array($_POST['tables'])) {
                foreach ($_POST['tables'] as $table) {
                    if (isset($table['entry_dates']) && !empty($table['entry_dates'][0])) {
                        $firstDate = $table['entry_dates'][0];
                        break;
                    }
                }
            } elseif (isset($_POST['entry_dates']) && !empty($_POST['entry_dates'][0])) {
                $firstDate = $_POST['entry_dates'][0];
            }
            
            // Insert main journal entry
            $query = "INSERT INTO journal_entries (journal_title, date, description, created_at) 
                      VALUES (:title, :date, :description, NOW())";
            
            $stmt = $this->db->prepare($query);
            
            // Create local variables for binding
            $title = 'Journal Entry';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            
            // Bind parameters
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':date', $firstDate);
            $stmt->bindParam(':description', $description);
            $stmt->execute();
            
            $journalId = $this->db->lastInsertId();
            
            // Insert journal details and update ledger
            $this->processJournalDetails($journalId, $_POST);
            
            // Update the title with the ID
            $query = "UPDATE journal_entries SET journal_title = :title WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $updatedTitle = 'Journal Entry #' . $journalId;
            $stmt->bindParam(':title', $updatedTitle);
            $stmt->bindParam(':id', $journalId);
            $stmt->execute();
            
            $this->db->commit();
            
            // Set success message
            $_SESSION['success_message'] = "Journal entry created successfully.";
            
            // Redirect to journal listing
            header('Location: ' . URL_ROOT . 'index.php?page=journal');
            exit;
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->handleError($e);
        }
    }
    
    // View single journal entry
    public function view($id) {
        try {
            // Fetch journal entry with total amount
            $query = "SELECT j.*, SUM(jd.debit) as total_amount 
                      FROM journal_entries j
                      LEFT JOIN journal_details jd ON j.id = jd.journal_id
                      WHERE j.id = :id
                      GROUP BY j.id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $journal = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$journal) {
                throw new Exception("Journal entry not found");
            }
            
            // Get journal details
            $query = "SELECT * FROM journal_details WHERE journal_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            include_once ROOT_PATH . 'views/journal/view.php';
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Edit journal entry
    public function edit($id) {
        try {
            // Get journal entry
            $query = "SELECT * FROM journal_entries WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $journal = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$journal) {
                throw new Exception("Journal entry not found");
            }
            
            // Get journal details
            $query = "SELECT * FROM journal_details WHERE journal_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $journal_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get all accounts for the dropdown
            $accounts = $this->ledger->getAccounts();
            
            include_once ROOT_PATH . 'views/journal/edit.php';
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Update journal entry
    public function update() {
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . URL_ROOT . 'index.php?page=journal');
            return;
        }
        
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            $journalId = $_POST['journal_id'];
            
            // Check if updated_at column exists
            $stmt = $this->db->query("SHOW COLUMNS FROM journal_entries LIKE 'updated_at'");
            $hasUpdatedAt = $stmt->rowCount() > 0;
            
            // Update main journal entry
            if ($hasUpdatedAt) {
                $query = "UPDATE journal_entries 
                         SET journal_title = :title, date = :date, updated_at = NOW()
                         WHERE id = :id";
            } else {
                $query = "UPDATE journal_entries 
                         SET journal_title = :title, date = :date
                         WHERE id = :id";
            }
            
            $stmt = $this->db->prepare($query);
            
            // Create local variables for binding
            $title = 'Journal Entry #' . $journalId;
            $firstDate = isset($_POST['entry_dates']) && !empty($_POST['entry_dates'][0]) ? 
                         $_POST['entry_dates'][0] : date('Y-m-d');
            
            // Bind parameters
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':date', $firstDate);
            $stmt->bindParam(':id', $journalId);
            $stmt->execute();
            
            // Delete existing journal details
            $query = "DELETE FROM journal_details WHERE journal_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $journalId);
            $stmt->execute();
            
            // Reinsert journal details and update ledger
            $this->processJournalDetails($journalId, $_POST);
            
            $this->db->commit();
            
            // Set success message
            $_SESSION['success_message'] = "Journal entry updated successfully.";
            
            // Redirect to journal listing
            header('Location: ' . URL_ROOT . 'index.php?page=journal');
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->handleError($e);
        }
    }
    
    // Delete journal entry
    public function delete($id) {
        try {
            // Log the delete attempt
            error_log("Attempting to delete journal entry with ID: $id");
            
            // Begin transaction
            $this->db->beginTransaction();
            
            // First, get all journal details to reverse ledger entries
            $query = "SELECT * FROM journal_details WHERE journal_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Found " . count($details) . " detail records to process");
            
            // Reverse ledger entries
            foreach ($details as $detail) {
                // Reverse the effect on the ledger - swap debit and credit
                if ($detail['debit'] > 0) {
                    error_log("Reversing debit of {$detail['debit']} for account {$detail['account']}");
                    $this->ledger->updateAccountBalance($detail['account'], $detail['debit'], 'credit');
                }
                if ($detail['credit'] > 0) {
                    error_log("Reversing credit of {$detail['credit']} for account {$detail['account']}");
                    $this->ledger->updateAccountBalance($detail['account'], $detail['credit'], 'debit');
                }
            }
            
            // First delete child records to avoid foreign key issues
            $query = "DELETE FROM journal_details WHERE journal_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            error_log("Deleted journal details result: " . ($result ? "success" : "failed"));
            
            // Delete journal entry
            $query = "DELETE FROM journal_entries WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            error_log("Deleted journal entry result: " . ($result ? "success" : "failed"));
            
            $this->db->commit();
            
            // Set success message
            $_SESSION['success_message'] = "Journal entry deleted successfully.";
            
            // Redirect back to journal listing
            header('Location: ' . URL_ROOT . 'index.php?page=journal');
            exit; // Ensure script stops execution after redirect
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error deleting journal entry: " . $e->getMessage());
            $this->handleError($e);
        }
    }
    
    // Process journal details (shared by store and update)
    private function processJournalDetails($journalId, $postData) {
        // Prepare statement for journal details
        $detailQuery = "INSERT INTO journal_details (journal_id, date, account, `reference`, debit, credit) 
                        VALUES (:journal_id, :date, :account, :reference, :debit, :credit)";
        $detailStmt = $this->db->prepare($detailQuery);
        
        // Check if we're using the table format structure
        if(isset($postData['tables']) && is_array($postData['tables'])) {
            foreach($postData['tables'] as $tableIndex => $table) {
                if(isset($table['accounts']) && is_array($table['accounts'])) {
                    foreach($table['accounts'] as $index => $account) {
                        // Skip empty rows completely
                        if(empty($account) && 
                           (empty($table['debits'][$index]) || $table['debits'][$index] == "0.00") && 
                           (empty($table['credits'][$index]) || $table['credits'][$index] == "0.00")) {
                            continue;
                        }
                        
                        // Preserve exactly what was entered
                        $date = isset($table['entry_dates'][$index]) ? $table['entry_dates'][$index] : date('Y-m-d');
                        $reference = isset($table['references'][$index]) ? $table['references'][$index] : '';
                        $debit = isset($table['debits'][$index]) && !empty($table['debits'][$index]) ? 
                                str_replace(',', '', $table['debits'][$index]) : 0;
                        $credit = isset($table['credits'][$index]) && !empty($table['credits'][$index]) ? 
                                str_replace(',', '', $table['credits'][$index]) : 0;
                        
                        // Bind values and execute
                        $detailStmt->bindValue(':journal_id', $journalId);
                        $detailStmt->bindValue(':date', $date);
                        $detailStmt->bindValue(':account', $account);
                        $detailStmt->bindValue(':reference', $reference);
                        $detailStmt->bindValue(':debit', $debit);
                        $detailStmt->bindValue(':credit', $credit);
                        $detailStmt->execute();
                        
                        // Update ledger if needed
                        if(!empty($account) && ($debit > 0 || $credit > 0)) {
                            try {
                                $this->ledger->ensureAccountExists($account);
                                if($debit > 0) {
                                    $this->ledger->updateAccountBalance($account, $debit, 'debit');
                                }
                                if($credit > 0) {
                                    $this->ledger->updateAccountBalance($account, $credit, 'credit');
                                }
                            } catch (Exception $e) {
                                error_log("Error updating ledger: " . $e->getMessage());
                            }
                        }
                    }
                }
            }
        } 
        // Fallback to traditional format
        else if(isset($postData['accounts']) && is_array($postData['accounts'])) {
            foreach($postData['accounts'] as $index => $account) {
                // Allow empty account names - no placeholder needed
                $accountName = $account; // Accept even empty values
                
                // Create local variables for binding
                $localJournalId = $journalId;
                $date = isset($postData['entry_dates'][$index]) && !empty($postData['entry_dates'][$index]) ? 
                       $postData['entry_dates'][$index] : date('Y-m-d');
                $reference = isset($postData['references'][$index]) ? $postData['references'][$index] : '';
                $debit = isset($postData['debits'][$index]) && !empty($postData['debits'][$index]) ? 
                         $postData['debits'][$index] : 0;
                $credit = isset($postData['credits'][$index]) && !empty($postData['credits'][$index]) ? 
                          $postData['credits'][$index] : 0;
                
                // Bind parameters using local variables
                $detailStmt->bindParam(':journal_id', $localJournalId);
                $detailStmt->bindParam(':date', $date);
                $detailStmt->bindParam(':account', $accountName);
                $detailStmt->bindParam(':reference', $reference);
                $detailStmt->bindParam(':debit', $debit);
                $detailStmt->bindParam(':credit', $credit);
                $detailStmt->execute();
                
                // Only update ledger if account name exists and amounts are provided
                if($debit > 0 || $credit > 0) {
                    try {
                        if(!empty($account)) {
                            $this->ledger->ensureAccountExists($account);
                            if($debit > 0) {
                                $this->ledger->updateAccountBalance($account, $debit, 'debit');
                            }
                            if($credit > 0) {
                                $this->ledger->updateAccountBalance($account, $credit, 'credit');
                            }
                        }
                    } catch (Exception $e) {
                        // Log error but continue with other accounts
                        error_log("Error updating ledger: " . $e->getMessage());
                    }
                }
            }
        }
    }
    
    // Check if required tables exist
    private function checkTablesExist() {
        try {
            // Check journal_details table
            $stmt = $this->db->query("SHOW TABLES LIKE 'journal_details'");
            if($stmt->rowCount() == 0) {
                // Create the table
                $this->db->exec("CREATE TABLE IF NOT EXISTS journal_details (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    journal_id INT NOT NULL,
                    date DATE NOT NULL,
                    account VARCHAR(100) NOT NULL,
                    `reference` VARCHAR(50),
                    debit DECIMAL(15,2) DEFAULT 0.00,
                    credit DECIMAL(15,2) DEFAULT 0.00,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (journal_id) REFERENCES journal_entries(id) ON DELETE CASCADE
                )");
            }
            
            // Check journal_title column in journal_entries
            $stmt = $this->db->query("SHOW COLUMNS FROM journal_entries LIKE 'journal_title'");
            if($stmt->rowCount() == 0) {
                // Add the column
                $this->db->exec("ALTER TABLE journal_entries ADD COLUMN journal_title VARCHAR(255) 
                                NOT NULL DEFAULT 'Journal Entry' AFTER id");
            }
            
            // Check updated_at column in journal_entries
            $stmt = $this->db->query("SHOW COLUMNS FROM journal_entries LIKE 'updated_at'");
            if($stmt->rowCount() == 0) {
                // Add the column
                $this->db->exec("ALTER TABLE journal_entries ADD COLUMN updated_at TIMESTAMP NULL");
            }
            
            // Check reference column in journal_details
            $stmt = $this->db->query("SHOW COLUMNS FROM journal_details LIKE 'reference'");
            if($stmt->rowCount() == 0) {
                // Add the column - escape reference with backticks
                $this->db->exec("ALTER TABLE journal_details ADD COLUMN `reference` VARCHAR(50) AFTER account");
            }
        } catch (Exception $e) {
            throw new Exception("Database structure error: " . $e->getMessage());
        }
    }
    
    // Centralized error handling
    private function handleError(Exception $e) {
        echo '<div class="alert alert-danger">';
        echo '<h4 class="alert-heading">Error</h4>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<hr>';
        echo '<p class="mb-0">Please <a href="' . URL_ROOT . 'setup.php">run the setup script</a> or contact the administrator.</p>';
        echo '</div>';
    }
}
?>
