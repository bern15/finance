<?php
// Admin logout page
require_once '../config/init.php';

// Unset admin session variable
unset($_SESSION['admin_logged_in']);

// Redirect to login page
header('Location: login.php');
exit;
?>
