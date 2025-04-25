<?php
// Main entry point for the application
require_once 'config/init.php';

// Include controller files (with error handling)
$controllers = [
    'DashboardController.php',
    'JournalController.php',
    'ComputationsController.php',
    'AuthController.php',
    'TrialBalanceController.php',
    'IncomeStatementController.php'
];

foreach ($controllers as $controller) {
    $controller_path = ROOT_PATH . 'controllers/' . $controller;
    if (file_exists($controller_path)) {
        require_once $controller_path;
    } else {
        echo '<div class="alert alert-danger">Controller file missing: ' . $controller . '</div>';
    }
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if tables exist
if ($db && !$database->tablesExist()) {
    // Tables don't exist, redirect to setup
    header('Location: ' . URL_ROOT . 'setup.php');
    exit;
}

// Determine which page to load
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Define which pages require authentication
$protected_pages = ['journal', 'computations', 'ledger', 'trial_balance', 'income_statement'];

// Check if current page requires authentication
if (in_array($page, $protected_pages) && !isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: ' . URL_ROOT . 'index.php?page=auth&action=login');
    exit;
}

// Route to appropriate controller
switch ($page) {
    case 'journal':
        if (class_exists('JournalController')) {
            $controller = new JournalController();
            
            if ($action == 'create') {
                $controller->create();
            } else if ($action == 'store') {
                $controller->store();
            } else if ($action == 'view' && isset($_GET['id'])) {
                $controller->view($_GET['id']);
            } else if ($action == 'edit' && isset($_GET['id'])) {
                $controller->edit($_GET['id']);
            } else if ($action == 'update') {
                $controller->update();
            } else if ($action == 'delete' && isset($_GET['id'])) {
                $controller->delete($_GET['id']);
            } else {
                $controller->index();
            }
        } else {
            echo '<div class="alert alert-danger">Journal functionality is not available.</div>';
        }
        break;
        
    case 'ledger':
        // Redirect ledger requests to computations page
        header('Location: ' . URL_ROOT . 'index.php?page=computations');
        exit;
        
    case 'computations':
        if (class_exists('ComputationsController')) {
            $controller = new ComputationsController();
            
            if ($action == 'cash') {
                $controller->cashReport();
            } else if ($action == 'general') {
                $controller->generalLedger();
            } else if ($action == 'template') {
                $accountName = isset($_GET['account']) ? $_GET['account'] : '';
                $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
                $controller->emptyTemplate($accountName, $rows);
            } else if ($action == 'save') {
                $controller->save();
            } else if ($action == 'delete') {
                $controller->delete();
            } else {
                $controller->index();
            }
        } else {
            echo '<div class="alert alert-danger">Accounts Title functionality is not available.</div>';
        }
        break;
        
    case 'auth':
        if (class_exists('AuthController')) {
            $controller = new AuthController();
            
            if ($action == 'login') {
                $controller->showLogin();
            } else if ($action == 'processLogin') {
                $controller->login();
            } else if ($action == 'register') {
                $controller->showRegister();
            } else if ($action == 'processRegister') {
                $controller->register();
            } else if ($action == 'logout') {
                $controller->logout();
            } else if ($action == 'profile') {
                $controller->profile();
            } else {
                $controller->showLogin();
            }
        } else {
            echo '<div class="alert alert-danger">Authentication functionality is not available.</div>';
        }
        break;
        
    case 'trial_balance':
        if (class_exists('TrialBalanceController')) {
            $controller = new TrialBalanceController();
            
            if ($action == 'create') {
                $controller->create();
            } else if ($action == 'store') {
                $controller->store();
            } else {
                $controller->index();
            }
        } else {
            echo '<div class="alert alert-danger">Trial Balance functionality is not available.</div>';
        }
        break;
        
    case 'income_statement':
        if (class_exists('IncomeStatementController')) {
            $controller = new IncomeStatementController();
            
            if ($action == 'store') {
                $controller->store();
            } else if ($action == 'calculate') {
                $controller->calculate();
            } else {
                $controller->index();
            }
        } else {
            echo '<div class="alert alert-danger">Income Statement functionality is not available.</div>';
        }
        break;
        
    default:
        if (class_exists('DashboardController')) {
            $controller = new DashboardController();
            $controller->index();
        } else {
            // Fallback if dashboard controller doesn't exist
            include_once ROOT_PATH . 'views/errors/missing_controller.php';
        }
        break;
}
?>
