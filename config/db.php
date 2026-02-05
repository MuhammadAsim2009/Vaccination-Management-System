<?php
// Database configuration
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'vaccination_management_system';

// Enable MySQLi exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Create a connection
$conn = new mysqli($host, $user, $password, $database);

?>