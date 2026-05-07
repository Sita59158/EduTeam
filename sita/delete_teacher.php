<?php
// delete_teacher.php - Delete Teacher
// Developer: Sita Subedi
// Module: Teacher Dashboard
// Project: Edu Team - Student Record System

session_start(); // Start session
include '../db.php'; // Include database connection
include 'Teacher.php'; // Include Teacher middle layer class

// Redirect to login if not logged in
if(!isset($_SESSION['teacher_id'])) {
    header('Location: ../isha/login.php');
    exit();
}

// Create Teacher object
$teacherObj = new Teacher($conn);

// Redirect if no teacher ID in URL
if(!isset($_GET['id'])) {
    header('Location: teacher_list.php');
    exit();
}

$teacher_id = $_GET['id']; // Get teacher ID from URL

// Use Teacher class deleteTeacher method
if($teacherObj->deleteTeacher($teacher_id)) {
    header('Location: teacher_list.php?success=Teacher deleted successfully!');
} else {
    header('Location: teacher_list.php?error=Error deleting teacher!');
}
exit();
?>