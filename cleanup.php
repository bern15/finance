<?php
// Direct cleanup script to immediately remove test accounts

require_once 'config/init.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

if(!$db) {
    die("Database connection failed. Please check your configuration.");
}

try {
    // Begin transaction
    $db->beginTransaction();
    
    // Standard account names to keep from setup
    $standardAccounts = [
        'Cash', 
        'Accounts Receivable', 
        'Office Equipment', 
        'Office Supplies',
        'Accounts Payable', 
        'Notes Payable',
        'Common Stock', 
        'Retained Earnings',
        'Service Revenue',
        'Rent Expense', 
        'Supplies Expense', 
        'Salaries Expense', 
        'Utilities Expense'
    ];
    
    // Convert to SQL format for IN clause
    $standardAccountsList = "'" . implode("','", $standardAccounts) . "'";
    
    // Delete all non-standard accounts
    $query = "DELETE FROM accounts WHERE account_name NOT IN ($standardAccountsList)";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $deletedCount = $stmt->rowCount();
    
    // Commit transaction
    $db->commit();
    
    // Store success message in session
    $_SESSION['success_message'] = "Ledger cleanup completed successfully. Removed $deletedCount test/dummy accounts.";
    
    // Redirect back to ledger
    header('Location: ' . URL_ROOT . 'index.php?page=ledger');
    exit;
} catch (Exception $e) {
    // Rollback on error
    if(isset($db)) {
        $db->rollBack();
    }
    echo "Error during cleanup: " . $e->getMessage();
}
?>
