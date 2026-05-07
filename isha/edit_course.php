<?php
// edit_course.php - Edit Course Page
// Developer: Isha | Module: User Authentication
// Project: Edu Team - Student Record System

session_start(); // Start session
include '../db.php'; // Include database connection
include 'Course.php'; // Include Course middle layer class

// Create Course object
$courseObj = new Course($conn);

$errors = []; // Store validation errors

// Redirect if no course ID in URL
if(!isset($_GET['id'])) {
    header('Location: course_list.php');
    exit();
}

$course_id = $_GET['id']; // Get course ID from URL

// Use Course class to get existing course data
$course = $courseObj->getCourse($course_id);

// Redirect if course not found
if(!$course) {
    header('Location: course_list.php');
    exit();
}

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get and sanitise form values
    $course_name = trim($_POST['course_name']);
    $course_code = trim($_POST['course_code']);
    $teacher_id = trim($_POST['teacher_id']);
    $is_active = $_POST['is_active'];
    $start_date = trim($_POST['start_date']);

    // Validate all required fields
    if($course_name == '') $errors[] = 'Course name is required!';
    if($course_code == '') $errors[] = 'Course code is required!';
    if($teacher_id == '') $errors[] = 'Teacher ID is required!';
    if($start_date == '') $errors[] = 'Start date is required!';

    // Only update if no validation errors
    if(empty($errors)) {
        // Prepare data array
        $data = [
            'course_name' => $course_name,
            'course_code' => $course_code,
            'teacher_id' => $teacher_id,
            'is_active' => $is_active,
            'start_date' => $start_date
        ];

        // Use Course class updateCourse method
        if($courseObj->updateCourse($course_id, $data)) {
            header('Location: course_list.php?success=Course updated successfully!');
            exit();
        } else {
            $errors[] = 'Error updating course!';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edu Team - Edit Course</title>
    <style>
        /* Reset browser default styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f0f5; display: flex; flex-direction: column; min-height: 100vh; }

        /* Purple navbar */
        .navbar { background: #4a235a; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
        .navbar span { color: white; font-size: 16px; font-weight: 500; }
        .navbar-right { display: flex; align-items: center; gap: 20px; }
        .navbar-right a { color: #ddd; font-size: 13px; text-decoration: none; }
        .avatar { width: 30px; height: 30px; border-radius: 50%; background: #6b3580; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 500; }

        /* Centres form on screen */
        .content { flex: 1; display: flex; justify-content: center; align-items: center; padding: 40px 24px; }
        .card { background: white; padding: 40px; border-radius: 12px; width: 480px; border: 1px solid #eee; }
        h1 { text-align: center; color: #4a235a; font-size: 22px; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #999; font-size: 13px; margin-bottom: 25px; }
        label { display: block; font-weight: 500; margin-bottom: 5px; color: #333; font-size: 14px; }
        input, select { width: 100%; padding: 11px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; margin-bottom: 15px; outline: none; }
        input:focus, select:focus { border-color: #4a235a; }

        /* Blue update button */
        .update-btn { width: 100%; padding: 13px; background: #1565c0; color: white; border: none; border-radius: 8px; font-size: 15px; cursor: pointer; }
        .update-btn:hover { background: #1976d2; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #4a235a; text-decoration: none; font-size: 13px; }

        /* Red error box */
        .error-box { background: #fce4ec; color: #c62828; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
        .error-box ul { margin-left: 16px; }
    </style>
</head>
<body>
    <div class="navbar">
        <span>Edu Team - Student Record System</span>
        <div class="navbar-right">
            <a href="../sita/dashboard.php">Dashboard</a>
            <a href="course_list.php">Courses</a>
            <a href="logout.php">Logout</a>
            <div class="avatar">ID</div>
        </div>
    </div>

    <div class="content">
        <div class="card">
            <h1>Edit Course</h1>
            <p class="subtitle">Developer: Isha | SRS-86</p>

            <?php
            // Show validation errors if any
            if(!empty($errors)) {
                echo '<div class="error-box"><ul>';
                foreach($errors as $error) echo '<li>' . $error . '</li>';
                echo '</ul></div>';
            }
            ?>

            <!-- Form pre-filled with existing course data -->
            <form method="POST">
                <label>Course Name:</label>
                <input type="text" name="course_name"
                    value="<?php echo $course['course_name']; ?>" required>

                <label>Course Code:</label>
                <input type="text" name="course_code"
                    value="<?php echo $course['course_code']; ?>" required>

                <label>Teacher ID:</label>
                <input type="number" name="teacher_id"
                    value="<?php echo $course['teacher_id']; ?>" required>

                <label>Start Date:</label>
                <input type="date" name="start_date"
                    value="<?php echo $course['start_date']; ?>" required>

                <!-- PHP echo selected on matching option -->
                <label>Status:</label>
                <select name="is_active">
                    <option value="1" <?php if($course['is_active'] == 1) echo 'selected'; ?>>Active</option>
                    <option value="0" <?php if($course['is_active'] == 0) echo 'selected'; ?>>Inactive</option>
                </select>

                <button type="submit" class="update-btn">Update Course</button>
                <a href="course_list.php" class="back-link">&larr; Back to All Courses</a>
            </form>
        </div>
    </div>
</body>
</html>