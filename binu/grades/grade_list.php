<?php
// grade_list.php - Grade List Page
// Developer: Binu
// Module: Grade Management
// Project: Edu Team - Student Record System

session_start();
include '../../db.php';

// Redirect to login if not logged in
if(!isset($_SESSION['teacher_id'])) {
    header('Location: ../../isha/login.php');
    exit();
}

// Get all grades from database
$result = $conn->query("SELECT * FROM grade ORDER BY grade_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edu Team - Grade Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f0f5; }
        .navbar { background: #4a235a; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-left { color: white; font-size: 16px; font-weight: 500; }
        .navbar-right { display: flex; align-items: center; gap: 20px; }
        .navbar-right a { color: #ddd; font-size: 13px; text-decoration: none; }
        .content { padding: 24px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 500; color: #333; margin-bottom: 4px; }
        .page-subtitle { font-size: 13px; color: #999; }
        .add-btn { background: #4a235a; color: white; padding: 10px 18px; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none; }
        .card { background: white; border: 1px solid #eee; border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead { background: #f9f9f9; }
        th { text-align: left; padding: 10px 16px; color: #999; font-weight: 500; }
        td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; }
        .edit-btn { background: #e3f2fd; color: #1565c0; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; margin-right: 4px; text-decoration: none; }
        .delete-btn { background: #fce4ec; color: #c62828; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; text-decoration: none; }
        .card-footer { padding: 12px 16px; border-top: 1px solid #eee; }
        .card-footer p { font-size: 12px; color: #999; }
        .developer { text-align: center; margin-top: 24px; font-size: 12px; color: #bbb; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; }
        .error { background: #fce4ec; color: #c62828; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="navbar">
        <span class="navbar-left">Edu Team - Student Record System</span>
        <div class="navbar-right">
            <a href="../../sita/dashboard.php">Dashboard</a>
            <a href="../../sita/teacher_list.php">Teachers</a>
            <a href="../../deepa/student_list.php">Students</a>
            <a href="../../isha/course_list.php">Courses</a>
            <a href="grade_list.php">Grades</a>
            <a href="../../satinder/attendance_list.php">Attendance</a>
            <a href="../../isha/logout.php">Logout</a>
        </div>
    </div>

    <div class="content">
        <?php
        if(isset($_GET['success'])) echo '<div class="success">' . $_GET['success'] . '</div>';
        if(isset($_GET['error'])) echo '<div class="error">' . $_GET['error'] . '</div>';
        ?>

        <div class="header">
            <div>
                <p class="page-title">Grade Management</p>
                <p class="page-subtitle">Developer: Binu | EduTeam</p>
            </div>
            <a href="add_grade.php" class="add-btn">+ Add New Grade</a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Teacher ID</th>
                        <th>Subject</th>
                        <th>Grade</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['grade_id'] . '</td>';
                            echo '<td>' . $row['student_id'] . '</td>';
                            echo '<td>' . $row['teacher_id'] . '</td>';
                            echo '<td>' . $row['subject'] . '</td>';
                            echo '<td>' . $row['grade'] . '</td>';
                            echo '<td>' . $row['remarks'] . '</td>';
                            echo '<td>';
                            echo '<a href="edit_grade.php?id=' . $row['grade_id'] . '" class="edit-btn">Edit</a>';
                            echo '<a href="delete_grade.php?id=' . $row['grade_id'] . '" class="delete-btn" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7" style="text-align:center;padding:20px;color:#999;">No grades found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div class="card-footer">
                <p>Total: <?php echo $result ? $result->num_rows : 0; ?> grades</p>
            </div>
        </div>
        <p class="developer">Developer: Binu | EduTeam</p>
    </div>
</body>
</html>