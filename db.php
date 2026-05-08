
Isha Dhakal
10:35 AM (1 minute ago)
to me

<?php
// Database Connection File
// Shared by all modules
// Project: Edu Team - Student Record System

$host = 'localhost';
$dbname = 'student_record_system';
$username = 'root';
$password = '';

// Create connection using mysqli_connect
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if(!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
?>
