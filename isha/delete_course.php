<?php
// delete_course.php - Delete Course
// Developer: Isha | Module: User Authentication
// Project: Edu Team - Student Record System

session_start(); // Start session
include '../db.php'; // Include database connection
include 'Course.php'; // Include Course middle layer class

// Create Course object
$courseObj = new Course($conn);

// Redirect if no course ID in URL
if(!isset($_GET['id'])) {
    header('Location: course_list.php');
    exit();
}

$course_id = $_GET['id']; // Get course ID from URL

// Use Course class deleteCourse method
if($courseObj->deleteCourse($course_id)) {
    header('Location: course_list.php?success=Course deleted successfully!');
} else {
    header('Location: course_list.php?error=Error deleting course!');
}
exit();
?>