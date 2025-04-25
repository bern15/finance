<?php
// Admin dashboard
require_once '../config/init.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get statistics
try {
    // User count
    $userCountQuery = "SELECT COUNT(*) as count FROM users";
    $stmt = $db->query($userCountQuery);
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Today's registrations
    $todayRegQuery = "SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()";
    $stmt = $db->query($todayRegQuery);
    $todayRegistrations = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Today's logins
    $todayLoginQuery = "SELECT COUNT(*) as count FROM user_activity WHERE activity_type = 'login' AND DATE(created_at) = CURDATE()";
    $stmt = $db->query($todayLoginQuery);
    $todayLogins = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Journal entries count
    $journalCountQuery = "SELECT COUNT(*) as count FROM journal_entries";
    $stmt = $db->query($journalCountQuery);
    $journalCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Computations count
    $computationsCountQuery = "SELECT COUNT(*) as count FROM computations";
    $stmt = $db->query($computationsCountQuery);
    $computationsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Get monthly stats for charts (past 6 months)
    $monthlyStatsQuery = "
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as count,
            'journal' as type
        FROM 
            journal_entries
        WHERE 
            created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY 
            DATE_FORMAT(created_at, '%Y-%m')
        
        UNION ALL
        
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as count,
            'computation' as type
        FROM 
            computations
        WHERE 
            created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY 
            DATE_FORMAT(created_at, '%Y-%m')
        
        ORDER BY 
            month, type
    ";
    
    $stmt = $db->query($monthlyStatsQuery);
    $monthlyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process data for charts
    $chartMonths = [];
    $chartJournalData = [];
    $chartComputationData = [];
    
    // Initialize last 6 months with 0 values
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i month"));
        $monthLabel = date('M Y', strtotime("-$i month"));
        $chartMonths[] = $monthLabel;
        $monthMap[$month] = count($chartMonths) - 1;
        $chartJournalData[] = 0;
        $chartComputationData[] = 0;
    }
    
    // Fill in actual values
    foreach ($monthlyStats as $stat) {
        if (isset($monthMap[$stat['month']])) {
            $idx = $monthMap[$stat['month']];
            if ($stat['type'] === 'journal') {
                $chartJournalData[$idx] = (int)$stat['count'];
            } else if ($stat['type'] === 'computation') {
                $chartComputationData[$idx] = (int)$stat['count'];
            }
        }
    }
    
    // Recent activity
    $recentActivityQuery = "SELECT * FROM user_activity ORDER BY created_at DESC LIMIT 5";
    $stmt = $db->query($recentActivityQuery);
    $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Handle database error
    $error = $e->getMessage();
}

include_once 'includes/header.php';
?>

<h1 class="mb-4">Admin Dashboard</h1>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <p><?= $error ?></p>
    </div>
<?php else: ?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <h2 class="mb-0"><?= $userCount ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Today's Registrations</h5>
                        <h2 class="mb-0"><?= $todayRegistrations ?></h2>
                    </div>
                    <i class="fas fa-user-plus fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Journal Entries</h5>
                        <h2 class="mb-0"><?= $journalCount ?></h2>
                    </div>
                    <i class="fas fa-book fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Computations</h5>
                        <h2 class="mb-0"><?= $computationsCount ?></h2>
                    </div>
                    <i class="fas fa-calculator fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Activity Trends (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Recent User Activity</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Activity</th>
                        <th>IP Address</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentActivity)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No recent activity</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentActivity as $activity): ?>
                            <tr>
                                <td><?= htmlspecialchars($activity['username'] ?? '') ?></td>
                                <td>
                                    <?php if ($activity['activity_type'] == 'login'): ?>
                                        <span class="badge badge-success">Login</span>
                                    <?php elseif ($activity['activity_type'] == 'logout'): ?>
                                        <span class="badge badge-danger">Logout</span>
                                    <?php elseif ($activity['activity_type'] == 'registration'): ?>
                                        <span class="badge badge-primary">Registration</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= htmlspecialchars($activity['activity_type'] ?? '') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= isset($activity['ip_address']) ? htmlspecialchars($activity['ip_address']) : 'Not recorded' ?></td>
                                <td><?= date('M d, Y g:i A', strtotime($activity['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-3">
            <a href="activity.php" class="btn btn-primary">View All Activity</a>
        </div>
    </div>
</div>

<!-- Chart JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly activity chart
    var ctx = document.getElementById('activityChart').getContext('2d');
    var activityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartMonths) ?>,
            datasets: [
                {
                    label: 'Journal Entries',
                    data: <?= json_encode($chartJournalData) ?>,
                    backgroundColor: 'rgba(23, 162, 184, 0.5)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Computations',
                    data: <?= json_encode($chartComputationData) ?>,
                    backgroundColor: 'rgba(255, 193, 7, 0.5)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>

<?php endif; ?>

<?php include_once 'includes/footer.php'; ?>
