<?php
// ============================================
// db.php - Database Connection File
// Shared by all modules
// Project: Edu Team - Student Record System
// Developer: EduTeam
// ============================================

// Database host
$host = 'localhost';

// Database name
$dbname = 'student_record_system';

// Database username
$username = 'root';

// Database password
$password = '';

// Create connection to MySQL
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection was successful
if($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
// Connection successful!
?>