<?php
class IncomeStatementController {
    private $db;
    
    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Display income statement page
    public function index() {
        try {
            // Get user ID from session
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            
            // Try to retrieve existing income statement data if available
            $incomeStatementData = $this->getIncomeStatementData($userId);
            
            include_once ROOT_PATH . 'views/income_statement/index.php';
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Save income statement data
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get form data
                $descriptions = $_POST['descriptions'] ?? [];
                $amounts = $_POST['amounts'] ?? [];
                
                // Get user ID from session
                $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
                
                // Prepare data for saving
                $data = [];
                for ($i = 0; $i < count($descriptions); $i++) {
                    // Only save rows with description or amount
                    if (!empty($descriptions[$i]) || (!empty($amounts[$i]) && $amounts[$i] != '0')) {
                        $data[] = [
                            'description' => $descriptions[$i],
                            'amount' => floatval($amounts[$i] ?? 0),
                            'user_id' => $userId
                        ];
                    }
                }
                
                // Save to database
                if ($this->saveIncomeStatementData($data, $userId)) {
                    $_SESSION['success_message'] = "Income Statement saved successfully.";
                } else {
                    $_SESSION['error_message'] = "Failed to save Income Statement.";
                }
                
            } catch (Exception $e) {
                $_SESSION['error_message'] = "Error: " . $e->getMessage();
            }
            
            // Redirect back to income statement page
            header('Location: ' . URL_ROOT . 'index.php?page=income_statement');
            exit;
        }
        
        // If not POST request, redirect to index
        header('Location: ' . URL_ROOT . 'index.php?page=income_statement');
        exit;
    }
    
    // Calculate the total
    public function calculate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get form data
                $descriptions = $_POST['descriptions'] ?? [];
                $amounts = $_POST['amounts'] ?? [];
                
                // Calculate total
                $total = 0;
                $items = [];
                
                for ($i = 0; $i < count($descriptions); $i++) {
                    $amount = floatval($amounts[$i] ?? 0);
                    $total += $amount;
                    
                    if (!empty($descriptions[$i]) || $amount > 0) {
                        $items[] = [
                            'description' => $descriptions[$i],
                            'amount' => $amount
                        ];
                    }
                }
                
                // Return JSON response with the total and items
                header('Content-Type: application/json');
                echo json_encode([
                    'total' => $total,
                    'items' => $items
                ]);
                exit;
                
            } catch (Exception $e) {
                // Return error as JSON
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
                exit;
            }
        }
        
        // If not POST request, redirect to index
        header('Location: ' . URL_ROOT . 'index.php?page=income_statement');
        exit;
    }
    
    // Helper method to get income statement data for a user
    private function getIncomeStatementData($userId) {
        try {
            $query = "SELECT * FROM income_statement WHERE user_id = :user_id ORDER BY id ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Check if table doesn't exist and create it
            if ($e->getCode() == '42S02') { // Table doesn't exist error code
                $this->createIncomeStatementTable();
                return [];
            }
            
            // Log other errors and return empty array
            error_log("Error fetching income statement data: " . $e->getMessage());
            return [];
        }
    }
    
    // Helper method to save income statement data
    private function saveIncomeStatementData($data, $userId) {
        try {
            // Begin transaction
            $this->db->beginTransaction();
            
            // Delete existing data for this user
            $deleteQuery = "DELETE FROM income_statement WHERE user_id = :user_id";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->bindParam(':user_id', $userId);
            $deleteStmt->execute();
            
            // Insert new data if we have any
            if (!empty($data)) {
                $insertQuery = "INSERT INTO income_statement (description, amount, user_id) VALUES (:description, :amount, :user_id)";
                $insertStmt = $this->db->prepare($insertQuery);
                
                foreach ($data as $item) {
                    $insertStmt->bindParam(':description', $item['description']);
                    $insertStmt->bindParam(':amount', $item['amount']);
                    $insertStmt->bindParam(':user_id', $item['user_id']);
                    $insertStmt->execute();
                }
            }
            
            // Commit transaction
            $this->db->commit();
            return true;
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            // Check if table doesn't exist and create it
            if ($e->getCode() == '42S02') { // Table doesn't exist error code
                $this->createIncomeStatementTable();
                // Try again after creating the table
                return $this->saveIncomeStatementData($data, $userId);
            }
            
            // Log error and return false
            error_log("Error saving income statement: " . $e->getMessage());
            return false;
        }
    }
    
    // Helper method to create income statement table if it doesn't exist
    private function createIncomeStatementTable() {
        try {
            $query = "CREATE TABLE IF NOT EXISTS income_statement (
                id INT AUTO_INCREMENT PRIMARY KEY,
                description VARCHAR(255),
                amount DECIMAL(15,2) DEFAULT 0.00,
                user_id INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )";
            
            $this->db->exec($query);
            
        } catch (PDOException $e) {
            error_log("Error creating income_statement table: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Error handling method
    private function handleError(Exception $e) {
        echo '<div class="alert alert-danger">';
        echo '<h4 class="alert-heading">Error</h4>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<hr>';
        echo '<p class="mb-0">Please check your database configuration or contact the administrator.</p>';
        echo '</div>';
    }
}
?>
