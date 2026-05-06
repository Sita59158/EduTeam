<?php
// dashboard.php - Teacher Dashboard
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

// Get logged in teacher details
$teacher_id = $_SESSION['teacher_id'];
$teacher = $teacherObj->getTeacher($teacher_id);

// Get dashboard statistics
$totalTeachers = $teacherObj->countAllTeachers();
$activeTeachers = $teacherObj->countActiveTeachers();
$enrolledStudents = $teacherObj->getEnrolledStudents($teacher_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edu Team - Teacher Dashboard</title>
    <style>
        /* Reset browser default styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f0f5; }

        /* Purple navbar - Edu Team brand colour */
        .navbar {
            background: #4a235a; padding: 14px 24px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .navbar-left { color: white; font-size: 16px; font-weight: 500; }
        .navbar-right { display: flex; align-items: center; gap: 20px; }
        .navbar-right a { color: #ddd; font-size: 13px; text-decoration: none; }

        /* Circle avatar showing user initials */
        .avatar { width: 30px; height: 30px; border-radius: 50%; background: #6b3580; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 500; }

        .content { padding: 24px; }

        /* Welcome message */
        .welcome {
            background: white; padding: 20px 24px;
            border-radius: 12px; border: 1px solid #eee;
            margin-bottom: 24px;
        }
        .welcome h2 { color: #4a235a; font-size: 18px; margin-bottom: 4px; }
        .welcome p { color: #999; font-size: 13px; }

        /* Stats cards row */
        .stats { display: flex; gap: 16px; margin-bottom: 24px; }

        /* Individual stat card */
        .stat-card {
            background: white; padding: 20px 24px;
            border-radius: 12px; border: 1px solid #eee;
            flex: 1; text-align: center;
        }
        .stat-number { font-size: 32px; font-weight: 700; color: #4a235a; }
        .stat-label { font-size: 13px; color: #999; margin-top: 4px; }

        /* Quick links section */
        .section-title { font-size: 16px; font-weight: 500; color: #333; margin-bottom: 16px; }

        /* Grid of quick link cards */
        .links-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }

        /* Quick link card */
        .link-card {
            background: white; padding: 20px;
            border-radius: 12px; border: 1px solid #eee;
            text-decoration: none; text-align: center;
            transition: border-color 0.2s;
        }
        .link-card:hover { border-color: #4a235a; }
        .link-card .icon { font-size: 28px; margin-bottom: 8px; }
        .link-card p { font-size: 14px; color: #333; font-weight: 500; }
        .link-card span { font-size: 12px; color: #999; }

        /* Developer info */
        .developer { text-align: center; margin-top: 24px; font-size: 12px; color: #bbb; }
    </style>
</head>
<body>
    <div class="navbar">
        <span class="navbar-left">Edu Team - Student Record System</span>
        <div class="navbar-right">
            <a href="dashboard.php">Dashboard</a>
            <a href="teacher_list.php">Teachers</a>
            <a href="../deepa/student_list.php">Students</a>
            <a href="../isha/course_list.php">Courses</a>
            <a href="../isha/logout.php">Logout</a>
            <!-- Show logged in teacher initials -->
            <div class="avatar">
                <?php echo strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)); ?>
            </div>
        </div>
    </div>

    <div class="content">

        <!-- Welcome message with teacher name from session -->
        <div class="welcome">
            <h2>Welcome, <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?>!</h2>
            <p>Teacher Dashboard — Edu Team Student Record System</p>
        </div>

        <!-- Stats cards showing real data from database -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalTeachers; ?></div>
                <div class="stat-label">Total Teachers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $activeTeachers; ?></div>
                <div class="stat-label">Active Teachers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $enrolledStudents; ?></div>
                <div class="stat-label">My Enrolled Students</div>
            </div>
        </div>

        <!-- Quick links to all modules -->
        <p class="section-title">Quick Links</p>
        <div class="links-grid">
            <a href="teacher_list.php" class="link-card">
                <div class="icon">👨‍🏫</div>
                <p>Teachers</p>
                <span>Manage teacher records</span>
            </a>
            <a href="../deepa/student_list.php" class="link-card">
                <div class="icon">👨‍🎓</div>
                <p>Students</p>
                <span>View enrolled students</span>
            </a>
            <a href="../isha/course_list.php" class="link-card">
                <div class="icon">📚</div>
                <p>Courses</p>
                <span>View course records</span>
            </a>
        </div>

        <p class="developer">Developer: Sita Subedi | SRS-83</p>
    </div>
</body>
</html>