<?php
class ComputationsController {
    private $db;
    private $computation;
    
    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Initialize computation model
        if ($this->db) {
            $this->computation = new Computation($this->db);
        }
    }
    
    // Display computations index page
    public function index() {
        try {
            // Only attempt to get computations if model exists
            $computations = isset($this->computation) ? $this->computation->getAll() : [];
            
            // Update page title to reflect new name
            $pageTitle = "Accounts Title";
            
            include_once ROOT_PATH . 'views/computations/index.php';
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Save computation data
    public function save() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form data
            $dr_value = isset($_POST['dr_value']) ? floatval($_POST['dr_value']) : 0;
            $cr_value = isset($_POST['cr_value']) ? floatval($_POST['cr_value']) : 0;
            $result = isset($_POST['result']) ? floatval($_POST['result']) : 0;
            
            if (isset($this->computation)) {
                $id = $this->computation->save($dr_value, $cr_value, $result);
                
                if($id) {
                    $_SESSION['success_message'] = "Computation saved successfully.";
                } else {
                    $_SESSION['error_message'] = "Failed to save computation.";
                }
            } else {
                $_SESSION['error_message'] = "Computation model not available.";
            }
            
            header('Location: ' . URL_ROOT . 'index.php?page=computations');
            exit;
        }
    }
    
    // Delete computation
    public function delete() {
        if(isset($_GET['id']) && isset($this->computation)) {
            $id = $_GET['id'];
            
            if($this->computation->delete($id)) {
                $_SESSION['success_message'] = "Computation deleted successfully.";
            } else {
                $_SESSION['error_message'] = "Failed to delete computation.";
            }
        }
        
        header('Location: ' . URL_ROOT . 'index.php?page=computations');
        exit;
    }
    
    // Display cash report view
    public function cashReport() {
        try {
            // Generate cash report data here
            $data = [
                'title' => 'Cash Flow Report',
                'totalDebits' => 0,
                'totalCredits' => 0,
                'finalBalance' => 0,
                'transactions' => []
            ];
            
            include_once ROOT_PATH . 'views/ledger/cash_report.php';
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Display general ledger view
    public function generalLedger() {
        try {
            include_once ROOT_PATH . 'views/ledger/general_ledger.php';
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Display empty template
    public function emptyTemplate($accountName = '', $rows = 10) {
        try {
            $data = [
                'title' => 'Ledger Template',
                'account_name' => $accountName,
                'rows' => $rows
            ];
            
            include_once ROOT_PATH . 'views/ledger/empty_ledger.php';
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Error handling method
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
