<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Account</h1>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="fa-stack fa-4x">
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas fa-user fa-stack-1x fa-inverse"></i>
                        </span>
                    </div>
                    <h4 class="text-center mb-3"><?= htmlspecialchars($_SESSION['username']) ?></h4>
                    <div class="list-group">
                        <div class="list-group-item">
                            <i class="fas fa-user-circle mr-2"></i> Username: <?= htmlspecialchars($_SESSION['username']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">My Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>IP Address</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($activities)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No activity recorded yet</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($activities as $activity): ?>
                                        <tr>
                                            <td>
                                                <?php if ($activity['activity_type'] == 'login'): ?>
                                                    <span class="badge badge-success">Login</span>
                                                <?php elseif ($activity['activity_type'] == 'logout'): ?>
                                                    <span class="badge badge-danger">Logout</span>
                                                <?php elseif ($activity['activity_type'] == 'registration'): ?>
                                                    <span class="badge badge-primary">Registration</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><?= htmlspecialchars($activity['activity_type']) ?></span>
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
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
