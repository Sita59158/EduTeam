<?php
// logout.php - Logout Page
// Developer: Isha | Module: User Authentication
// Project: Edu Team - Student Record System

session_start(); // Start session

// Clear all session variables
$_SESSION = array();

// Destroy the session completely
session_destroy();

// Redirect to login page after logout
header('Location: login.php');
exit();
?>