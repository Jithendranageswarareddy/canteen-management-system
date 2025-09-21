<?php
// Database connection for College Canteen Management System
require_once 'config.php';

// Connect to MySQL database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection and report errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
