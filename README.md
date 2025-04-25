
# 💰 Finance Management System (PHP MVC)

A lightweight, modular web-based **Accounting & Finance Management System** built with PHP. It helps users manage journals, ledgers, computations, trial balances, and income statements using a clean, extendable MVC architecture.

---

## 🗂️ Project Structure

```
finance/
├── config/                     # App configuration (DB, constants)
├── controllers/               # Controller classes (Business logic)
├── models/                    # Model classes (Database interaction)
├── views/                     # UI templates and pages
├── assets/                    # CSS, JS, and static assets
├── includes/                  # Reusable components (e.g., header, footer)
├── index.php                  # Main entry point (routing + auth)
├── setup.php                  # First-run database setup
├── database_connection.php    # DB connection class
├── *.php                      # Utility/maintenance scripts
└── database_setup.sql         # SQL dump to initialize schema
```

---

## 🚀 Features

- 🔐 **User Authentication** – Secure login, registration, and profile system
- 📓 **Journal Entries** – Add, edit, delete, and view financial journals
- 📘 **Ledger Management** – Generate and maintain ledger records
- 🧮 **Computations Module** – Generate cash reports, general ledgers, and templates
- 🧾 **Trial Balance Generator** – Automatic computation of trial balances
- 📈 **Income Statement** – Auto-calculate income summary based on entries
- ✅ **MVC Pattern** – Organized and modular architecture
- 💾 **SQL Schema Included** – Easy setup via `database_setup.sql`

---

## ⚙️ Installation Guide

### 1. Clone the Repository

```bash
git clone https://github.com/bern15/finance.git
```

### 2. Setup Your Environment

Make sure your local server supports:

- PHP 7.4+
- MySQL/MariaDB
- Apache/Nginx

Place the project in your server directory (e.g., `htdocs/` for XAMPP).

### 3. Configure Database

Create a new database and import the SQL schema:

```bash
# In phpMyAdmin or MySQL CLI
CREATE DATABASE finance_db;
```

Then import the file:
```
database_setup.sql
```

Edit database credentials in `config/init.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'finance_db');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_pass');
```

### 4. Access the System

Open your browser and go to:

```
http://localhost/finance/index.php
```

If the database isn’t set up yet, the app will redirect to `setup.php`.

---

## 🧭 Routing Overview

Routing is handled via query parameters:

```
index.php?page={controller}&action={method}
```

Examples:

- `?page=auth&action=login`
- `?page=journal&action=create`
- `?page=income_statement&action=calculate`

---

## 🔐 Authentication System

Pages such as Journal, Ledger, Trial Balance, and Computations are protected. Users must be logged in to access them.

Session-based authentication checks for `$_SESSION['user_id']`.

---

## ⚡ Utility Scripts

- `setup.php` – Initializes DB tables
- `purge_accounts.php` – Clears account data
- `create_computations_table.php` – Creates computations table
- `cleanup_ledger.php` – Cleans old ledger entries
- `cleanup.php` – General cleanup script

---

## 📜 License

This project is licensed under the [MIT License](LICENSE).

---

## 🙌 Contribution

Feel free to fork this repo, suggest features, or submit pull requests.

---

## 📬 Contact

For questions or ideas:
- GitHub Issues
