<?php
$host = 'localhost';
$user = 'root';
$password = 'admin@123';
$database = $_GET['database']; // Get the selected database from the query parameter

// Validate the database name
if (empty($database)) {
    die("Database name is required.");
}

// Create a connection to the database
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the backup file name
$backupFile = "{$database}_backup.sql";

// Open the backup file for writing
$handle = fopen($backupFile, 'w+');

// Get all tables from the database
$tables = $conn->query("SHOW TABLES");

while ($row = $tables->fetch_row()) {
    $table = $row[0];
    $result = $conn->query("SELECT * FROM $table");
    $numColumns = $result->field_count;

    // Write table structure to the backup file
    $conn->query("LOCK TABLES $table WRITE");
    $conn->query("DROP TABLE IF EXISTS $table");
    $createTable = $conn->query("SHOW CREATE TABLE $table");
    $createTableRow = $createTable->fetch_row();
    fwrite($handle, $createTableRow[1] . ";\n\n");

    // Write table data to the backup file
    while ($row = $result->fetch_row()) {
        fwrite($handle, "INSERT INTO $table VALUES(");
        for ($i = 0; $i < $numColumns; $i++) {
            $row[$i] = addslashes($row[$i]);
            $row[$i] = str_replace("\n", "\\n", $row[$i]);
            if (isset($row[$i])) {
                fwrite($handle, "'" . $row[$i] . "'");
            } else {
                fwrite($handle, "NULL");
            }
            if ($i < ($numColumns - 1)) {
                fwrite($handle, ",");
            }
        }
        fwrite($handle, ");\n");
    }
    fwrite($handle, "\n\n");
    $conn->query("UNLOCK TABLES");
}

// Close the file and the database connection
fclose($handle);
$conn->close();

// Validate the backup file
if (!file_exists($backupFile)) {
    die("Backup file not found.");
}

if (filesize($backupFile) === 0) {
    unlink($backupFile); // Delete the empty file
    die("Backup file is empty.");
}

// Send the backup file to the client
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($backupFile));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($backupFile));
readfile($backupFile);

// Delete the backup file after sending
unlink($backupFile);
exit;
?>