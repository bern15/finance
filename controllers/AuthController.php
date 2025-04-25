<?php
class AuthController {
    private $db;
    private $user;
    
    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Initialize user model
        $this->user = new User($this->db);
    }
    
    // Show login form
    public function showLogin() {
        // Check if user is already logged in
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . URL_ROOT);
            exit;
        }
        
        include_once ROOT_PATH . 'views/auth/login.php';
    }
    
    // Process login
    public function login() {
        // Check if form was submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Validate form data
            if(empty($username) || empty($password)) {
                $_SESSION['error'] = "Please fill in all fields";
                header('Location: ' . URL_ROOT . 'index.php?page=auth&action=login');
                exit;
            }
            
            // Set user properties
            $this->user->username = $username;
            $this->user->password = $password;
            
            // Attempt login
            if($this->user->login()) {
                // Set session variables
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['username'] = $this->user->username;
                
                // Log the login activity
                $this->user->logActivity('login');
                
                // Redirect to dashboard
                header('Location: ' . URL_ROOT);
                exit;
            } else {
                // Set error message
                $_SESSION['error'] = "Invalid username or password";
                header('Location: ' . URL_ROOT . 'index.php?page=auth&action=login');
                exit;
            }
        }
        
        // If not a POST request, redirect to login form
        header('Location: ' . URL_ROOT . 'index.php?page=auth&action=login');
    }
    
    // Show registration form
    public function showRegister() {
        // Check if user is already logged in
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . URL_ROOT);
            exit;
        }
        
        include_once ROOT_PATH . 'views/auth/register.php';
    }
    
    // Process registration
    public function register() {
        // Check if form was submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Validate form data
            if(empty($username) || empty($password) || empty($confirm_password)) {
                $_SESSION['error'] = "Please fill in all fields";
                header('Location: ' . URL_ROOT . 'index.php?page=auth&action=register');
                exit;
            }
            
            // Check if passwords match
            if($password !== $confirm_password) {
                $_SESSION['error'] = "Passwords do not match";
                header('Location: ' . URL_ROOT . 'index.php?page=auth&action=register');
                exit;
            }
            
            // Set user properties
            $this->user->username = $username;
            $this->user->password = $password;
            
            // Register user
            if($this->user->register()) {
                // Log the registration activity
                $this->user->logActivity('registration');
                
                // Set success message
                $_SESSION['success'] = "Registration successful! You can now login.";
                header('Location: ' . URL_ROOT . 'index.php?page=auth&action=login');
                exit;
            } else {
                // Set error message
                $_SESSION['error'] = "Username already exists";
                header('Location: ' . URL_ROOT . 'index.php?page=auth&action=register');
                exit;
            }
        }
        
        // If not a POST request, redirect to registration form
        header('Location: ' . URL_ROOT . 'index.php?page=auth&action=register');
    }
    
    // Logout
    public function logout() {
        // Ensure user info is set before logging
        if (isset($_SESSION['user_id'])) {
            $this->user->id = $_SESSION['user_id'];
            if (isset($_SESSION['username'])) {
                $this->user->username = $_SESSION['username'];
            }
            
            // Log the logout activity
            try {
                $this->user->logActivity('logout');
            } catch (Exception $e) {
                // Log the error but continue with logout
                error_log("Error logging logout: " . $e->getMessage());
            }
        }
        
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header('Location: ' . URL_ROOT . 'index.php?page=auth&action=login');
        exit;
    }
    
    // Show user profile with activity history
    public function profile() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URL_ROOT . 'index.php?page=auth&action=login');
            exit;
        }
        
        // Get user's activity history
        $userId = $_SESSION['user_id'];
        $activities = $this->user->getUserActivity($userId);
        
        include_once ROOT_PATH . 'views/auth/profile.php';
    }
    
    // Check if user is logged in - middleware function
    public static function isLoggedIn() {
        if(!isset($_SESSION['user_id'])) {
            return false;
        }
        return true;
    }
    
    // Redirect if not logged in - middleware function
    public static function requireLogin() {
        if(!self::isLoggedIn()) {
            header('Location: ' . URL_ROOT . 'index.php?page=auth&action=login');
            exit;
        }
    }
}
?>
