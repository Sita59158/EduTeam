<?php
// edit_student.php - Edit Student Page
// Developer: Deepa Thapa | Module: Student Profile Management
// Project: Edu Team - Student Record System

session_start(); // Start session
include '../db.php'; // Include database connection
include 'Student.php'; // Include Student middle layer class

// Create Student object
$studentObj = new Student($conn);

$errors = []; // Array to store validation errors

// Redirect if no student ID in URL
if(!isset($_GET['id'])) {
    header('Location: student_list.php');
    exit();
}

$student_id = $_GET['id']; // Get student ID from URL

// Use Student class to fetch existing student data
$student = $studentObj->getStudent($student_id);

// Redirect if student not found
if(!$student) {
    header('Location: student_list.php');
    exit();
}

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get and sanitise form values
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $teacher_id = trim($_POST['teacher_id']);
    $course_id = trim($_POST['course_id']);
    $enrolled_date = trim($_POST['enrolled_date']);
    $is_active = $_POST['is_active'];

    // Validate all required fields
    if($full_name == '') $errors[] = 'Full name is required!';
    if($email == '') $errors[] = 'Email is required!';
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email format is not valid!';
    if($teacher_id == '') $errors[] = 'Teacher ID is required!';
    if($course_id == '') $errors[] = 'Course ID is required!';
    if($enrolled_date == '') $errors[] = 'Enrolled date is required!';

    // Only update if no validation errors
    if(empty($errors)) {
        // Prepare data array for Student class
        $data = [
            'full_name' => $full_name,
            'email' => $email,
            'teacher_id' => $teacher_id,
            'course_id' => $course_id,
            'enrolled_date' => $enrolled_date,
            'is_active' => $is_active
        ];

        // Use Student class updateStudent method
        if($studentObj->updateStudent($student_id, $data)) {
            header('Location: student_list.php?success=Student updated successfully!');
            exit();
        } else {
            $errors[] = 'Error updating student!';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edu Team - Edit Student</title>
    <style>
        /* Reset browser default styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f0f5; display: flex; flex-direction: column; min-height: 100vh; }

        /* Purple navbar - Edu Team brand colour */
        .navbar { background: #4a235a; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
        .navbar span { color: white; font-size: 16px; font-weight: 500; }
        .navbar-right { display: flex; align-items: center; gap: 20px; }
        .navbar-right a { color: #ddd; font-size: 13px; text-decoration: none; }

        /* Circle avatar for user initials */
        .avatar { width: 30px; height: 30px; border-radius: 50%; background: #6b3580; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 500; }

        /* Centres form on screen */
        .content { flex: 1; display: flex; justify-content: center; align-items: center; padding: 40px 24px; }

        /* White card for form */
        .card { background: white; padding: 40px; border-radius: 12px; width: 480px; border: 1px solid #eee; }
        h1 { text-align: center; color: #4a235a; font-size: 22px; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #999; font-size: 13px; margin-bottom: 25px; }
        label { display: block; font-weight: 500; margin-bottom: 5px; color: #333; font-size: 14px; }
        input, select { width: 100%; padding: 11px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; margin-bottom: 15px; outline: none; }
        input:focus, select:focus { border-color: #4a235a; }

        /* Blue update button - different from Add */
        .update-btn { width: 100%; padding: 13px; background: #1565c0; color: white; border: none; border-radius: 8px; font-size: 15px; cursor: pointer; }
        .update-btn:hover { background: #1976d2; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #4a235a; text-decoration: none; font-size: 13px; }

        /* Red error box for validation messages */
        .error-box { background: #fce4ec; color: #c62828; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
        .error-box ul { margin-left: 16px; }
    </style>
</head>
<body>
    <div class="navbar">
        <span>Edu Team - Student Record System</span>
        <div class="navbar-right">
            <a href="../sita/dashboard.php">Dashboard</a>
            <a href="student_list.php">Students</a>
            <a href="../isha/logout.php">Logout</a>
            <div class="avatar">DT</div>
        </div>
    </div>

    <div class="content">
        <div class="card">
            <h1>Edit Student</h1>
            <p class="subtitle">Developer: Deepa Thapa | SRS-84</p>

            <?php
            // Show validation errors if any
            if(!empty($errors)) {
                echo '<div class="error-box"><ul>';
                foreach($errors as $error) echo '<li>' . $error . '</li>';
                echo '</ul></div>';
            }
            ?>

            <!-- Form pre-filled with existing student data -->
            <form method="POST">
                <label>Full Name:</label>
                <input type="text" name="full_name"
                    value="<?php echo $student['full_name']; ?>" required>

                <label>Email:</label>
                <input type="email" name="email"
                    value="<?php echo $student['email']; ?>" required>

                <label>Teacher ID:</label>
                <input type="number" name="teacher_id"
                    value="<?php echo $student['teacher_id']; ?>" required>

                <label>Course ID:</label>
                <input type="number" name="course_id"
                    value="<?php echo $student['course_id']; ?>" required>

                <label>Enrolled Date:</label>
                <input type="date" name="enrolled_date"
                    value="<?php echo $student['enrolled_date']; ?>" required>

                <!-- PHP echo selected on matching option -->
                <label>Status:</label>
                <select name="is_active">
                    <option value="1" <?php if($student['is_active'] == 1) echo 'selected'; ?>>Active</option>
                    <option value="0" <?php if($student['is_active'] == 0) echo 'selected'; ?>>Inactive</option>
                </select>

                <button type="submit" class="update-btn">Update Student</button>
                <a href="student_list.php" class="back-link">&larr; Back to All Students</a>
            </form>
        </div>
    </div>
</body>
</html>