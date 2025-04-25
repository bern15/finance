-- Drop database if it exists and create a new one
DROP DATABASE IF EXISTS finance;
CREATE DATABASE finance;
USE finance;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- User activity tracking
CREATE TABLE user_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(50),
    activity_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Chart of Accounts
CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_name VARCHAR(100) NOT NULL UNIQUE,
    account_type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    balance DECIMAL(15,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (account_type)
) ENGINE=InnoDB;

-- Journal entries (header)
CREATE TABLE journal_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_title VARCHAR(255) NOT NULL DEFAULT 'Journal Entry',
    date DATE NOT NULL,
    description TEXT,
    debit_account VARCHAR(100),
    credit_account VARCHAR(100),
    amount DECIMAL(15,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    user_id INT,
    INDEX (date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Journal details (line items)
CREATE TABLE journal_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_id INT NOT NULL,
    date DATE NOT NULL,
    account VARCHAR(100) NOT NULL,
    reference VARCHAR(50),
    debit DECIMAL(15,2) DEFAULT 0.00,
    credit DECIMAL(15,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (date),
    INDEX (account),
    FOREIGN KEY (journal_id) REFERENCES journal_entries(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Computations history
CREATE TABLE computations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dr_value DECIMAL(15,2) DEFAULT 0.00,
    cr_value DECIMAL(15,2) DEFAULT 0.00,
    result DECIMAL(15,2) DEFAULT 0.00,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Insert default chart of accounts
INSERT INTO accounts (account_name, account_type, balance) VALUES
-- Asset accounts
('Cash', 'asset', 0.00),
('Accounts Receivable', 'asset', 0.00),
('Office Equipment', 'asset', 0.00),
('Office Supplies', 'asset', 0.00),

-- Liability accounts
('Accounts Payable', 'liability', 0.00),
('Notes Payable', 'liability', 0.00),

-- Equity accounts
('Common Stock', 'equity', 0.00),
('Retained Earnings', 'equity', 0.00),

-- Revenue accounts
('Service Revenue', 'revenue', 0.00),

-- Expense accounts
('Rent Expense', 'expense', 0.00),
('Supplies Expense', 'expense', 0.00),
('Salaries Expense', 'expense', 0.00),
('Utilities Expense', 'expense', 0.00);

-- Create admin user (password: 123)
INSERT INTO users (username, password) VALUES
('admin', '$2y$10$Ojs0GSxhz3WYIlYiKi94LuXhE5ZwSUAHRz7RY8E/vkeVDPO16N3u6');
