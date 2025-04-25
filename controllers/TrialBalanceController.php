<?php
class TrialBalanceController {
    private $db;
    private $trialBalance;
    
    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Initialize trial balance model
        if ($this->db) {
            $this->trialBalance = new TrialBalance($this->db);
        }
    }
    
    // Display trial balance index page (empty form)
    public function index() {
        try {
            // Get user ID from session
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            
            // Check if user has existing trial balance data
            $trialBalanceData = [];
            if ($userId && isset($this->trialBalance)) {
                $trialBalanceData = $this->trialBalance->getByUserId($userId);
            }
            
            include_once ROOT_PATH . 'views/trial_balance/index.php';
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Process and store trial balance data
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get form data
                $accounts = $_POST['accounts'] ?? [];
                $trialBalanceDebits = $_POST['trial_balance_debit'] ?? [];
                $trialBalanceCredits = $_POST['trial_balance_credit'] ?? [];
                $adjustmentDebits = $_POST['adjustment_debit'] ?? [];
                $adjustmentCredits = $_POST['adjustment_credit'] ?? [];
                $adjustedBalanceDebits = $_POST['adjusted_balance_debit'] ?? [];
                $adjustedBalanceCredits = $_POST['adjusted_balance_credit'] ?? [];
                $incomeStatementDebits = $_POST['income_statement_debit'] ?? [];
                $incomeStatementCredits = $_POST['income_statement_credit'] ?? [];
                $balanceSheetDebits = $_POST['balance_sheet_debit'] ?? [];
                $balanceSheetCredits = $_POST['balance_sheet_credit'] ?? [];
                
                // Get user ID from session
                $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
                
                // Validate data
                if (empty($accounts)) {
                    $_SESSION['error_message'] = "Please provide at least one account.";
                    header('Location: ' . URL_ROOT . 'index.php?page=trial_balance');
                    exit;
                }
                
                // Prepare data for saving
                $data = [];
                for ($i = 0; $i < count($accounts); $i++) {
                    if (empty($accounts[$i])) continue;
                    
                    $data[] = [
                        'account_name' => $accounts[$i],
                        'trial_balance_debit' => floatval($trialBalanceDebits[$i] ?? 0),
                        'trial_balance_credit' => floatval($trialBalanceCredits[$i] ?? 0),
                        'adjustment_debit' => floatval($adjustmentDebits[$i] ?? 0),
                        'adjustment_credit' => floatval($adjustmentCredits[$i] ?? 0),
                        'adjusted_balance_debit' => floatval($adjustedBalanceDebits[$i] ?? 0),
                        'adjusted_balance_credit' => floatval($adjustedBalanceCredits[$i] ?? 0),
                        'income_statement_debit' => floatval($incomeStatementDebits[$i] ?? 0),
                        'income_statement_credit' => floatval($incomeStatementCredits[$i] ?? 0),
                        'balance_sheet_debit' => floatval($balanceSheetDebits[$i] ?? 0),
                        'balance_sheet_credit' => floatval($balanceSheetCredits[$i] ?? 0),
                        'user_id' => $userId
                    ];
                }
                
                // Save to database
                if (isset($this->trialBalance)) {
                    $result = $this->trialBalance->saveTrialBalance($data, $userId);
                    
                    if ($result) {
                        $_SESSION['success_message'] = "Trial balance saved successfully.";
                    } else {
                        $_SESSION['error_message'] = "Failed to save trial balance.";
                    }
                } else {
                    $_SESSION['error_message'] = "Trial balance model not available.";
                }
                
            } catch (Exception $e) {
                $_SESSION['error_message'] = "Error: " . $e->getMessage();
            }
            
            // Redirect back to trial balance page
            header('Location: ' . URL_ROOT . 'index.php?page=trial_balance');
            exit;
        }
        
        // If not POST request, redirect to index
        header('Location: ' . URL_ROOT . 'index.php?page=trial_balance');
        exit;
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
