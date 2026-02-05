# Patient Management REST API

A RESTful API built with Core PHP and MySQL for managing patient records.

## 🚀 Setup Instructions

### 1. Clone/Copy Project
Copy the `patient-api` folder to your `htdocs` (XAMPP) or `www` (WAMP) directory.

### 2. Create Database
- Open phpMyAdmin
- Run the SQL from the database setup section

### 3. Configure Database
Edit `api/config/database.php` with your credentials:
```php
private $host = "localhost";
private $db_name = "hospital_db";
private $username = "root";
private $password = "";