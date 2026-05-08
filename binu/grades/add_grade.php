<?php
// add_grade.php - Add New Grade
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

$errors = [];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = trim($_POST['student_id']);
    $teacher_id = $_SESSION['teacher_id'];
    $subject    = trim($_POST['subject']);
    $grade      = trim($_POST['grade']);
    $remarks    = trim($_POST['remarks']);

    // Validate fields
    if($student_id == '') $errors[] = 'Student ID is required.';
    if($subject == '')    $errors[] = 'Subject is required.';
    if($grade == '')      $errors[] = 'Grade is required.';

    if(empty($errors)) {
        $query = "INSERT INTO grade (student_id, teacher_id, subject, grade, remarks) 
                  VALUES ('$student_id', '$teacher_id', '$subject', '$grade', '$remarks')";
        if($conn->query($query)) {
            header('Location: grade_list.php?success=Grade added successfully!');
            exit();
        } else {
            $errors[] = 'Failed to add grade.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edu Team - Add Grade</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f0f5; }
        .navbar { background: #4a235a; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-left { color: white; font-size: 16px; font-weight: 500; }
        .navbar-right { display: flex; align-items: center; gap: 20px; }
        .navbar-right a { color: #ddd; font-size: 13px; text-decoration: none; }
        .content { padding: 24px; max-width: 600px; }
        .page-title { font-size: 20px; font-weight: 500; color: #333; margin-bottom: 20px; }
        .card { background: white; border: 1px solid #eee; border-radius: 12px; padding: 24px; }
        label { display: block; font-size: 13px; font-weight: 500; color: #333; margin-bottom: 6px; }
        input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; margin-bottom: 16px; outline: none; }
        input:focus, select:focus, textarea:focus { border-color: #4a235a; }
        .btn-row { display: flex; gap: 10px; }
        .save-btn { background: #4a235a; color: white; padding: 10px 24px; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; }
        .cancel-btn { background: #f5f5f5; color: #333; padding: 10px 24px; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none; }
        .error { background: #fce4ec; color: #c62828; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
        .developer { text-align: center; margin-top: 24px; font-size: 12px; color: #bbb; }
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
        <p class="page-title">Add New Grade</p>

        <?php if(!empty($errors)) { foreach($errors as $error) { echo '<div class="error">' . $error . '</div>'; } } ?>

        <div class="card">
            <form method="POST">
                <label>Student ID:</label>
                <input type="number" name="student_id" placeholder="Enter student ID" value="<?php echo isset($_POST['student_id']) ? $_POST['student_id'] : ''; ?>">

                <label>Subject:</label>
                <input type="text" name="subject" placeholder="e.g. Mathematics" value="<?php echo isset($_POST['subject']) ? $_POST['subject'] : ''; ?>">

                <label>Grade:</label>
                <select name="grade">
                    <option value="">Select Grade</option>
                    <option value="A+" <?php echo (isset($_POST['grade']) && $_POST['grade']=='A+') ? 'selected' : ''; ?>>A+</option>
                    <option value="A" <?php echo (isset($_POST['grade']) && $_POST['grade']=='A') ? 'selected' : ''; ?>>A</option>
                    <option value="B+" <?php echo (isset($_POST['grade']) && $_POST['grade']=='B+') ? 'selected' : ''; ?>>B+</option>
                    <option value="B" <?php echo (isset($_POST['grade']) && $_POST['grade']=='B') ? 'selected' : ''; ?>>B</option>
                    <option value="C+" <?php echo (isset($_POST['grade']) && $_POST['grade']=='C+') ? 'selected' : ''; ?>>C+</option>
                    <option value="C" <?php echo (isset($_POST['grade']) && $_POST['grade']=='C') ? 'selected' : ''; ?>>C</option>
                    <option value="D" <?php echo (isset($_POST['grade']) && $_POST['grade']=='D') ? 'selected' : ''; ?>>D</option>
                    <option value="F" <?php echo (isset($_POST['grade']) && $_POST['grade']=='F') ? 'selected' : ''; ?>>F</option>
                </select>

                <label>Remarks:</label>
                <textarea name="remarks" placeholder="Enter remarks..." rows="3"><?php echo isset($_POST['remarks']) ? $_POST['remarks'] : ''; ?></textarea>

                <div class="btn-row">
                    <button type="submit" class="save-btn">Save Grade</button>
                    <a href="grade_list.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
        <p class="developer">Developer: Binu | EduTeam</p>
    </div>
</body>
</html>