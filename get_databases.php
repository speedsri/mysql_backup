<?php
$host = 'localhost';
$user = 'root';
$password = 'admin@123';

// Create a connection to the MySQL server
$conn = new mysqli($host, $user, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the list of databases
$result = $conn->query("SHOW DATABASES");

if (!$result) {
    die("Error fetching databases: " . $conn->error);
}

$databases = [];
while ($row = $result->fetch_row()) {
    $databases[] = $row[0];
}

// Return the list of databases as JSON
header('Content-Type: application/json');
echo json_encode($databases);

$conn->close();
?>