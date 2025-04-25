<?php 
include_once ROOT_PATH . 'includes/header.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize variables with default values
$journalCount = 0;
$accountCount = 0;
$accountTypeTotals = ['asset' => 0, 'liability' => 0, 'equity' => 0, 'revenue' => 0, 'expense' => 0];
$recentEntries = [];

// Only proceed with database queries if connection is successful and tables exist
if($db && $database->tablesExist()) {
    try {
        // Get some stats for the dashboard
        $journalCount = $db->query("SELECT COUNT(*) as count FROM journal_entries")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        $accountCount = $db->query("SELECT COUNT(*) as count FROM accounts")->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

        // Get totals by account type
        $accountTypes = ['asset', 'liability', 'equity', 'revenue', 'expense'];

        foreach($accountTypes as $type) {
            $query = "SELECT SUM(balance) as total FROM accounts WHERE account_type = :type";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':type', $type);
            $stmt->execute();
            $accountTypeTotals[$type] = abs($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
        }

        // Get recent journal entries
        $recentEntries = $db->query("SELECT * FROM journal_entries ORDER BY date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Silently handle errors to prevent breaking the page
        // In production, you might want to log this error
    }
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Financial Dashboard</h1>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow text-center mb-4">
                <div class="card-body">
                    <div class="dashboard-widget widget-primary">
                        <i class="fas fa-book fa-3x mb-3"></i>
                        <h4>Journal Entries</h4>
                        <h2><?= $journalCount ?></h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card shadow text-center mb-4">
                <div class="card-body">
                    <div class="dashboard-widget widget-success">
                        <i class="fas fa-file-invoice-dollar fa-3x mb-3"></i>
                        <h4>Accounts</h4>
                        <h2><?= $accountCount ?></h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card shadow text-center mb-4">
                <div class="card-body">
                    <div class="dashboard-widget widget-info">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <h4>Assets</h4>
                        <h2>$<?= number_format($accountTypeTotals['asset'], 2) ?></h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card shadow text-center mb-4">
                <div class="card-body">
                    <div class="dashboard-widget widget-warning">
                        <i class="fas fa-hand-holding-usd fa-3x mb-3"></i>
                        <h4>Revenue</h4>
                        <h2>$<?= number_format($accountTypeTotals['revenue'], 2) ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Recent Journal Entries</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Accounts</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($recentEntries)): ?>
                                    <?php foreach($recentEntries as $entry): ?>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime($entry['date'])) ?></td>
                                            <td><?= $entry['description'] ?></td>
                                            <td><?= $entry['debit_account'] ?> / <?= $entry['credit_account'] ?></td>
                                            <td class="text-right">$<?= number_format($entry['amount'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No journal entries found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= URL_ROOT ?>index.php?page=journal" class="btn btn-primary">View All Entries</a>
                        <a href="<?= URL_ROOT ?>index.php?page=journal&action=create" class="btn btn-success">Create New Entry</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Account Balances</h3>
                </div>
                <div class="card-body">
                    <canvas id="accountTypeChart" width="400" height="300"></canvas>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3>Quick Links</h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="<?= URL_ROOT ?>index.php?page=journal&action=create" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus-circle mr-2"></i> Add Journal Entry
                        </a>
                        <a href="<?= URL_ROOT ?>index.php?page=ledger" class="list-group-item list-group-item-action">
                            <i class="fas fa-book-open mr-2"></i> View Ledger Accounts
                        </a>
                        <a href="<?= URL_ROOT ?>index.php?page=trial-balance" class="list-group-item list-group-item-action">
                            <i class="fas fa-balance-scale mr-2"></i> Generate Trial Balance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Create chart for account balances
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('accountTypeChart').getContext('2d');
    const accountChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Assets', 'Liabilities', 'Equity', 'Revenue', 'Expenses'],
            datasets: [{
                data: [
                    <?= $accountTypeTotals['asset'] ?>,
                    <?= $accountTypeTotals['liability'] ?>,
                    <?= $accountTypeTotals['equity'] ?>,
                    <?= $accountTypeTotals['revenue'] ?>,
                    <?= $accountTypeTotals['expense'] ?>
                ],
                backgroundColor: [
                    '#007bff',
                    '#dc3545',
                    '#28a745',
                    '#ffc107',
                    '#6c757d'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            legend: {
                position: 'bottom'
            }
        }
    });
});
</script>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
