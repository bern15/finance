<?php
// Admin users page
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

// Get all users
$users = $user->getAll();

include_once 'includes/header.php';
?>

<h1 class="mb-4">User Management</h1>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Registered Users</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="3" class="text-center">No users found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $userItem): ?>
                            <tr>
                                <td><?= $userItem['id'] ?></td>
                                <td><?= htmlspecialchars($userItem['username']) ?></td>
                                <td><?= date('M d, Y g:i A', strtotime($userItem['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
