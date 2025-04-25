<?php
class DashboardController {
    private $db;
    
    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Display dashboard with summary information
    public function index() {
        try {
            // Get total number of journal entries
            $query = "SELECT COUNT(*) as entry_count FROM journal_entries";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $journalCount = $stmt->fetch(PDO::FETCH_ASSOC)['entry_count'] ?? 0;
            
            // Get total number of accounts
            $query = "SELECT COUNT(*) as account_count FROM accounts";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $accountCount = $stmt->fetch(PDO::FETCH_ASSOC)['account_count'] ?? 0;
            
            // Get total number of computations
            $query = "SELECT COUNT(*) as comp_count FROM computations";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $computationCount = $stmt->fetch(PDO::FETCH_ASSOC)['comp_count'] ?? 0;
            
            // Get account balances summary by type
            $query = "SELECT 
                        account_type, 
                        SUM(balance) as total_balance
                      FROM 
                        accounts
                      GROUP BY 
                        account_type";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $accountSummary = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get monthly stats for charts (past 6 months) for current user
            $userId = $_SESSION['user_id'] ?? 0;
            
            // Only attempt to get chart data if user is logged in
            if ($userId) {
                // Monthly journals
                $journalChartQuery = "
                    SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        DATE_FORMAT(created_at, '%b %Y') as month_label,
                        COUNT(*) as count
                    FROM 
                        journal_entries
                    WHERE 
                        created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    GROUP BY 
                        DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %Y')
                    ORDER BY 
                        month
                ";
                
                $stmt = $this->db->prepare($journalChartQuery);
                $stmt->execute();
                $journalChartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Monthly computations
                $computationChartQuery = "
                    SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        DATE_FORMAT(created_at, '%b %Y') as month_label,
                        COUNT(*) as count
                    FROM 
                        computations
                    WHERE 
                        created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    GROUP BY 
                        DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %Y')
                    ORDER BY 
                        month
                ";
                
                $stmt = $this->db->prepare($computationChartQuery);
                $stmt->execute();
                $computationChartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Process data for charts
                $chartMonths = [];
                $chartJournalData = [];
                $chartComputationData = [];
                
                // Initialize last 6 months with 0 values
                for ($i = 5; $i >= 0; $i--) {
                    $monthLabel = date('M Y', strtotime("-$i month"));
                    $chartMonths[] = $monthLabel;
                    $chartJournalData[] = 0;
                    $chartComputationData[] = 0;
                }
                
                // Map month to index for quick lookup
                $monthMap = [];
                foreach ($chartMonths as $idx => $month) {
                    $monthMap[date('Y-m', strtotime("01 $month"))] = $idx;
                }
                
                // Fill in journal data
                foreach ($journalChartData as $data) {
                    if (isset($monthMap[$data['month']])) {
                        $chartJournalData[$monthMap[$data['month']]] = (int)$data['count'];
                    }
                }
                
                // Fill in computation data
                foreach ($computationChartData as $data) {
                    if (isset($monthMap[$data['month']])) {
                        $chartComputationData[$monthMap[$data['month']]] = (int)$data['count'];
                    }
                }
            }
            
            include_once ROOT_PATH . 'views/dashboard/index.php';
        } catch (Exception $e) {
            $this->handleError($e);
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
