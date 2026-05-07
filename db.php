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
$conn = mysqli_connect($host, $username, 
                       $password, $dbname);

// Check connection was successful
if(!$conn) {
    die('Connection failed: ' . 
    mysqli_connect_error());
}
// Connection successful!
?>