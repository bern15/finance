<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Accounting System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= URL_ROOT ?>assets/css/style.css">
    
    <!-- Page specific CSS -->
    <?php
    // Determine which CSS file to include based on page
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    
    switch($page) {
        case 'journal':
            echo '<link rel="stylesheet" href="' . URL_ROOT . 'assets/css/journal.css">';
            break;
        case 'computations':
        case 'ledger':
            echo '<link rel="stylesheet" href="' . URL_ROOT . 'assets/css/computations.css">';
            break;
        case 'trial_balance':
            echo '<link rel="stylesheet" href="' . URL_ROOT . 'assets/css/trial_balance.css">';
            break;
        case 'auth':
            echo '<link rel="stylesheet" href="' . URL_ROOT . 'assets/css/auth.css">';
            break;
        default:
            echo '<link rel="stylesheet" href="' . URL_ROOT . 'assets/css/dashboard.css">';
            break;
    }
    ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= URL_ROOT ?>">
                <i class="fas fa-calculator mr-2"></i>
                Financial Accounting System
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav mr-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URL_ROOT ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URL_ROOT ?>index.php?page=journal">Journal Entries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URL_ROOT ?>index.php?page=computations">Accounts Title</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URL_ROOT ?>index.php?page=trial_balance">Trial Balance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URL_ROOT ?>index.php?page=income_statement">Income Statement</a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-user-circle mr-1"></i> <?= $_SESSION['username'] ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="<?= URL_ROOT ?>index.php?page=auth&action=profile">
                                    <i class="fas fa-user mr-1"></i> My Account
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= URL_ROOT ?>index.php?page=auth&action=logout">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                </a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URL_ROOT ?>index.php?page=auth&action=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URL_ROOT ?>index.php?page=auth&action=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>
