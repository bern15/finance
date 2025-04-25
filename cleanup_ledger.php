<?php
// Direct script to clean up all test accounts without confirmation
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
    
    // Standard account names to keep
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
    
    // Delete all non-standard accounts (anything not in our standard list)
    echo "<h2>Running ledger cleanup...</h2>";
    echo "<pre>";
    echo "Keeping standard accounts: " . implode(", ", $standardAccounts) . "\n\n";
    
    // First get all accounts to be deleted (for reporting)
    $query = "SELECT * FROM accounts WHERE account_name NOT IN ($standardAccountsList)";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $accountsToDelete = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($accountsToDelete) . " non-standard accounts to remove:\n";
    foreach($accountsToDelete as $account) {
        echo "- " . $account['account_name'] . " (Type: " . $account['account_type'] . ", Balance: " . $account['balance'] . ")\n";
    }
    
    // Now perform the deletion
    $query = "DELETE FROM accounts WHERE account_name NOT IN ($standardAccountsList)";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $deletedCount = $stmt->rowCount();
    
    // Commit transaction
    $db->commit();
    
    echo "\nCleanup complete! Successfully removed $deletedCount test/dummy accounts.\n";
    echo "</pre>";
    
    echo "<p><a href='" . URL_ROOT . "index.php?page=ledger' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;'>Return to Ledger</a></p>";
    
} catch (Exception $e) {
    // Rollback on error
    if(isset($db)) {
        $db->rollBack();
    }
    echo "<h2>Error during cleanup</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><a href='" . URL_ROOT . "'>Return to Dashboard</a></p>";
}
?>
