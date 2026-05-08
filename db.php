<?php
// Database Connection File
// Shared by all modules
// Project: Edu Team - Student Record System

$host = 'localhost';
$dbname = 'student_record_system';
$username = 'root';
$password = '';

// mysqli_connect for Deepa Isha Sita Satinder
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if(!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

// Object style for Binu
$mysqli = new mysqli($host, $username, $password, $dbname);
?>