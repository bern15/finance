<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Financial Dashboard</h1>
        <?php if(isset($_SESSION['user_id'])): ?>
        <div>
            <a href="<?= URL_ROOT ?>index.php?page=journal" class="btn btn-primary mr-2">
                <i class="fas fa-file-alt"></i> Journal Entries
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php if(!isset($_SESSION['user_id'])): ?>
    <div class="alert alert-info">
        <h4><i class="fas fa-info-circle"></i> Welcome to Financial Accounting System</h4>
        <p>Please <a href="<?= URL_ROOT ?>index.php?page=auth&action=login">login</a> or <a href="<?= URL_ROOT ?>index.php?page=auth&action=register">register</a> to access the system features.</p>
    </div>
    <?php else: ?>
    <div class="row">
        <!-- Summary Cards -->
        <div class="col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Journal Entries</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $journalCount ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Accounts Title History</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $computationCount ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activity Charts -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Activity Overview (Last 6 Months)</h5>
        </div>
        <div class="card-body">
            <canvas id="activityChart" height="100"></canvas>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if(isset($_SESSION['user_id'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity chart
    var ctx = document.getElementById('activityChart').getContext('2d');
    var activityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartMonths) ?>,
            datasets: [
                {
                    label: 'Journal Entries',
                    data: <?= json_encode($chartJournalData) ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.5)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Computations',
                    data: <?= json_encode($chartComputationData) ?>,
                    backgroundColor: 'rgba(54, 185, 204, 0.5)',
                    borderColor: 'rgba(54, 185, 204, 1)',
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

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
