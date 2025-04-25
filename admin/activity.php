<?php
// Admin user activity page
require_once '../config/init.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create User model
$user = new User($db);

// Get all user activity
$activities = $user->getAllActivity();

include_once 'includes/header.php';
?>

<h1 class="mb-4">User Activity Log</h1>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Activity History</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Activity</th>
                        <th>IP Address</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activities)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No activity found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td><?= $activity['id'] ?></td>
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
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
