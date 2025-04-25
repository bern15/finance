<?php
// Admin login page
require_once '../config/init.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    // Simple admin password validation - in a real app, this would be more secure
    if ($password === '123') {
        // Set admin session
        $_SESSION['admin_logged_in'] = true;
        
        // Redirect to admin dashboard
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid admin password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Financial Accounting System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="m-0"><i class="fas fa-user-shield mr-2"></i>Admin Login</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="password">Admin Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <a href="<?= URL_ROOT ?>" class="text-decoration-none">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Main Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
