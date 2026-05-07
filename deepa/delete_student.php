<?php
// delete_student.php - Delete Student
// Developer: Deepa Thapa | Module: Student Profile Management
// Project: Edu Team - Student Record System

session_start(); // Start session
include '../db.php'; // Include database connection
include 'Student.php'; // Include Student middle layer class

// Create Student object
$studentObj = new Student($conn);

// Redirect if no student ID in URL
if(!isset($_GET['id'])) {
    header('Location: student_list.php');
    exit();
}

$student_id = $_GET['id']; // Get student ID from URL

// Use Student class deleteStudent method
if($studentObj->deleteStudent($student_id)) {
    header('Location: student_list.php?success=Student deleted successfully!');
} else {
    header('Location: student_list.php?error=Error deleting student!');
}
exit();
?>