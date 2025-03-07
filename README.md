# MySQL Database Backup System
## Documentation and Installation Guide

![Database Backup Banner](https://github.com/speedsri/mysql_backup/blob/main/sql_backup2.png)

## 📑 Table of Contents
- [Overview](#overview)
- [System Requirements](#system-requirements)
- [Installation Guide](#installation-guide)
- [File Structure](#file-structure)
- [How It Works](#how-it-works)
- [Configuration](#configuration)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## 📋 Overview

This MySQL Database Backup System provides a simple and elegant web interface for backing up your MySQL databases. The system allows users to:

1. View all available databases on the server
2. Select a specific database to back up
3. Download the backup as a .sql file

The system uses a combination of HTML, CSS (with Tailwind CSS framework), JavaScript, and PHP to create a seamless backup experience.

## 💻 System Requirements

- Web server (Apache, Nginx, etc.)
- PHP 7.0 or higher
- MySQL/MariaDB server
- Modern web browser with JavaScript enabled

## 🚀 Installation Guide

### Step 1: Clone the Repository

```bash
git clone https://github.com/username/mysql-backup-system.git
cd mysql-backup-system
```

### Step 2: Configure Database Connection

Create two PHP files: `get_databases.php` and `backup.php` with the database credentials.

**get_databases.php**:
```php
<?php
// Database credentials
$host = "your_host";       // e.g., localhost
$user = "your_username";   // MySQL username
$password = "your_password"; // MySQL password

// Create connection
$conn = new mysqli($host, $user, $password);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get databases
$result = $conn->query("SHOW DATABASES");
$databases = [];

while ($row = $result->fetch_assoc()) {
    // Exclude system databases (optional)
    if (!in_array($row['Database'], ['information_schema', 'performance_schema', 'mysql', 'sys'])) {
        $databases[] = $row['Database'];
    }
}

// Return database names as JSON
header('Content-Type: application/json');
echo json_encode($databases);

$conn->close();
?>
```

**backup.php**:
```php
<?php
// Database credentials
$host = "your_host";       // e.g., localhost
$user = "your_username";   // MySQL username
$password = "your_password"; // MySQL password

// Get selected database name
$database = $_GET['database'] ?? '';

if (empty($database)) {
    die("No database selected");
}

// Set headers for file download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $database . '_backup.sql"');

// Execute mysqldump command
$command = "mysqldump -h $host -u $user";
if (!empty($password)) {
    $command .= " -p'$password'";
}
$command .= " $database";

// Output the dump
passthru($command);
?>
```

### Step 3: Deploy to Web Server

Upload the following files to your web server:
- `index.html` (the main interface)
- `get_databases.php` (configured with your database credentials)
- `backup.php` (configured with your database credentials)

### Step 4: Set Proper Permissions

Make sure your web server has the necessary permissions to execute the PHP files and the `mysqldump` command.

```bash
chmod 755 get_databases.php backup.php
```

### Step 5: Access the Application

Open your web browser and navigate to:
```
http://your-server-address/path-to-application/index.html
```

## 📁 File Structure

```
mysql-backup-system/
├── index.html         # Main user interface
├── get_databases.php  # API to fetch available databases
├── backup.php         # API to create and download database backups
└── README.md          # Documentation
```

## ⚙️ How It Works

1. **Database Discovery**:
   - When the page loads, the system sends a request to `get_databases.php`
   - PHP connects to MySQL and retrieves a list of available databases
   - The databases are displayed in a dropdown menu

2. **Backup Process**:
   - User selects a database from the dropdown
   - User clicks the "Download Backup" button
   - JavaScript sends a request to `backup.php` with the selected database name
   - PHP executes `mysqldump` to create a SQL backup file
   - The backup is streamed to the browser as a download

3. **User Interface**:
   - The interface provides visual feedback during the backup process
   - A loading spinner appears while the backup is being created
   - Success/failure messages are displayed after the operation completes

## ⚡ Configuration

### Database Credentials

You need to update the database credentials in both PHP files:

```php
$host = "your_host";       // Your MySQL host (e.g., localhost)
$user = "your_username";   // Your MySQL username
$password = "your_password"; // Your MySQL password
```

### Excluding System Databases (Optional)

In `get_databases.php`, you can exclude system databases by modifying the condition:

```php
if (!in_array($row['Database'], ['information_schema', 'performance_schema', 'mysql', 'sys'])) {
    $databases[] = $row['Database'];
}
```

## 🔍 Troubleshooting

### Common Issues:

1. **Database Connection Failed**
   - Verify your database credentials
   - Ensure MySQL server is running
   - Check if the MySQL user has proper permissions

2. **No Databases Shown**
   - Confirm MySQL user has permission to view databases
   - Check PHP error logs for detailed error messages

3. **Backup Download Fails**
   - Ensure PHP has permission to execute the `mysqldump` command
   - Verify that `mysqldump` is installed on the server
   - Check the server's PHP execution timeout settings

4. **UI Display Issues**
   - Make sure Tailwind CSS and Font Awesome CDN links are accessible
   - Clear browser cache or try a different browser

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.
