<?php
// Database setup script to initialize the application

require_once 'config/init.php';

// Create database and get connection
$database = new Database();
$db = $database->getConnection();

if(!$db) {
    die("<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
        <h2 style='color: #dc3545;'>Database Connection Failed</h2>
        <p>Could not connect to MySQL server or create the database. Please check your MySQL configuration.</p>
        </div>");
}

// Check if tables already exist
$tablesExist = false;
try {
    // Check for journal_entries table
    $stmt = $db->query("SHOW TABLES LIKE 'journal_entries'");
    $journalEntriesExists = $stmt->rowCount() > 0;
    
    // Check for journal_details table
    $stmt = $db->query("SHOW TABLES LIKE 'journal_details'");
    $journalDetailsExists = $stmt->rowCount() > 0;
    
    // Check for accounts table
    $stmt = $db->query("SHOW TABLES LIKE 'accounts'");
    $accountsExists = $stmt->rowCount() > 0;
    
    $tablesExist = $journalEntriesExists && $journalDetailsExists && $accountsExists;
} catch (PDOException $e) {
    // Tables don't exist or other error
    $tablesExist = false;
}

// Only show "Tables Already Exist" if not resetting and tables truly exist
if($tablesExist && !isset($_GET['reset'])) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
        <h2 style='color: #28a745;'>Tables Already Exist</h2>
        <p>The database tables are already set up.</p>
        <p><a href='index.php' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;'>Access the Application</a></p>
        <p>If you want to reset the database, you can <a href='setup.php?reset=true' style='color: #dc3545;'>click here</a>.</p>
        </div>";
    
    // Don't exit, so the user can still proceed if needed
    if(!isset($_GET['force'])) {
        // Optional parameter to force continuing execution
        echo "<p style='text-align: center;'><a href='setup.php?force=1' style='color: #6c757d; font-size: 0.9em;'>Force setup to run anyway</a></p>";
        exit;
    }
}

// If reset is requested, drop existing tables
if(isset($_GET['reset']) && $_GET['reset'] === 'true') {
    try {
        $db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $db->exec("DROP TABLE IF EXISTS journal_details");
        $db->exec("DROP TABLE IF EXISTS journal_entries");
        $db->exec("DROP TABLE IF EXISTS accounts");
        $db->exec("DROP TABLE IF EXISTS computations");
        $db->exec("DROP TABLE IF EXISTS users");
        $db->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        echo "<div style='background-color: #ffc; padding: 10px; margin: 10px 0; border: 1px solid #cc0;'>
              <p>Database has been reset. Tables will now be recreated.</p>
              </div>";
    } catch (PDOException $e) {
        echo "<div style='background-color: #fcc; padding: 10px; margin: 10px 0; border: 1px solid #c00;'>
              <p>Error resetting database: " . $e->getMessage() . "</p>
              </div>";
    }
}

// Create tables
$tables = [
    // Journal entries table (updated with updated_at column)
    "CREATE TABLE IF NOT EXISTS journal_entries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        journal_title VARCHAR(255) NOT NULL DEFAULT 'Journal Entry',
        date DATE NOT NULL,
        description TEXT,
        debit_account VARCHAR(100),
        credit_account VARCHAR(100),
        amount DECIMAL(15,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL
    )",
    
    // Journal details table (new)
    "CREATE TABLE IF NOT EXISTS journal_details (
        id INT AUTO_INCREMENT PRIMARY KEY,
        journal_id INT NOT NULL,
        date DATE NOT NULL,
        account VARCHAR(100) NOT NULL,
        debit DECIMAL(15,2) DEFAULT 0.00,
        credit DECIMAL(15,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (journal_id) REFERENCES journal_entries(id) ON DELETE CASCADE
    )",
    
    // Accounts table
    "CREATE TABLE IF NOT EXISTS accounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        account_name VARCHAR(100) NOT NULL UNIQUE,
        account_type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
        balance DECIMAL(15,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Computations table (new)
    "CREATE TABLE IF NOT EXISTS computations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dr_value DECIMAL(15,2) DEFAULT 0.00,
        cr_value DECIMAL(15,2) DEFAULT 0.00,
        result DECIMAL(15,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Users table (new)
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

// Execute table creation
$success = true;
foreach($tables as $sql) {
    try {
        $db->exec($sql);
    } catch(PDOException $e) {
        echo "Error creating table: " . $e->getMessage() . "<br>";
        $success = false;
    }
}

// Check for schema updates needed on existing tables
try {
    // Check if journal_entries table exists but needs updating
    $tableExists = $db->query("SHOW TABLES LIKE 'journal_entries'")->rowCount() > 0;
    
    if ($tableExists) {
        // Check if updated_at column exists
        $columnExists = $db->query("SHOW COLUMNS FROM journal_entries LIKE 'updated_at'")->rowCount() > 0;
        
        if (!$columnExists) {
            // Add updated_at column
            $db->exec("ALTER TABLE journal_entries ADD COLUMN updated_at TIMESTAMP NULL");
            echo "<div style='background-color: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb;'>
                 <p>Database updated: Added 'updated_at' column to journal_entries table.</p>
                 </div>";
        }
    }
} catch (PDOException $e) {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;'>
         <p>Error updating database structure: " . $e->getMessage() . "</p>
         </div>";
}

// Migrate data from old structure to new structure if needed
try {
    // Check if old data exists but new table is empty
    $oldEntryCount = $db->query("SELECT COUNT(*) FROM journal_entries WHERE debit_account IS NOT NULL")->fetchColumn();
    $newDetailCount = $db->query("SELECT COUNT(*) FROM journal_details")->fetchColumn();
    
    if ($oldEntryCount > 0 && $newDetailCount == 0) {
        echo "<div style='background-color: #ffc; padding: 10px; margin: 10px 0; border: 1px solid #cc0;'>
              <p>Migrating existing journal entries to the new data structure...</p>";
        
        // Migrate old entries to new format
        $oldEntries = $db->query("SELECT * FROM journal_entries WHERE debit_account IS NOT NULL");
        while ($entry = $oldEntries->fetch(PDO::FETCH_ASSOC)) {
            // First, update the journal entry with a title
            $db->exec("UPDATE journal_entries SET 
                      journal_title = 'Journal Entry #" . $entry['id'] . "' 
                      WHERE id = " . $entry['id']);
            
            // Then create detail records
            $db->exec("INSERT INTO journal_details 
                      (journal_id, date, account, debit, credit) VALUES
                      (" . $entry['id'] . ", '" . $entry['date'] . "', '" . $entry['debit_account'] . "', " . $entry['amount'] . ", 0),
                      (" . $entry['id'] . ", '" . $entry['date'] . "', '" . $entry['credit_account'] . "', 0, " . $entry['amount'] . ")");
        }
        
        echo "<p>Migration completed successfully!</p></div>";
    }
} catch (PDOException $e) {
    echo "<div style='background-color: #fcc; padding: 10px; margin: 10px 0; border: 1px solid #c00;'>
          <p>Migration error: " . $e->getMessage() . "</p></div>";
}

// Insert sample data if tables were created successfully
if($success) {
    // Sample accounts
    $accounts = [
        // Asset accounts
        ['Cash', 'asset'],
        ['Accounts Receivable', 'asset'],
        ['Office Equipment', 'asset'],
        ['Office Supplies', 'asset'],
        
        // Liability accounts
        ['Accounts Payable', 'liability'],
        ['Notes Payable', 'liability'],
        
        // Equity accounts
        ['Common Stock', 'equity'],
        ['Retained Earnings', 'equity'],
        
        // Revenue accounts
        ['Service Revenue', 'revenue'],
        
        // Expense accounts
        ['Rent Expense', 'expense'],
        ['Supplies Expense', 'expense'],
        ['Salaries Expense', 'expense'],
        ['Utilities Expense', 'expense']
    ];
    
    // Insert accounts
    $accountQuery = "INSERT INTO accounts (account_name, account_type) VALUES (?, ?)";
    $stmt = $db->prepare($accountQuery);
    
    foreach($accounts as $account) {
        try {
            $stmt->execute($account);
        } catch(PDOException $e) {
            // Ignore duplicate entry errors
            if($e->getCode() != 23000) {
                echo "Error inserting account: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<h2 style='color: #28a745;'>Setup Completed Successfully!</h2>";
    echo "<p>Database tables have been created and sample data has been loaded.</p>";
    echo "<p>You can now <a href='index.php' style='color: #007bff;'>access the application</a>.</p>";
    echo "</div>";
} else {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<h2 style='color: #dc3545;'>Setup Failed</h2>";
    echo "<p>There was an error creating the database tables. Please check the error messages above.</p>";
    echo "</div>";
}

echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
    <h2 style='color: #28a745;'>Setup Completed</h2>
    <p>Database tables have been " . ($tablesExist ? "verified" : "created") . ".</p>
    <p><a href='index.php' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;'>Start Using the Application</a></p>
    </div>";
?>
