<?php
// edit_teacher.php - Edit Teacher Page
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
$errors = []; // Store validation errors

// Redirect if no teacher ID in URL
if(!isset($_GET['id'])) {
    header('Location: teacher_list.php');
    exit();
}

$teacher_id = $_GET['id']; // Get teacher ID from URL

// Use Teacher class to get existing teacher data
$teacher = $teacherObj->getTeacher($teacher_id);

// Redirect if teacher not found
if(!$teacher) {
    header('Location: teacher_list.php');
    exit();
}

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get and sanitise form values
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $is_active = $_POST['is_active'];

    // Validate all required fields
    if($first_name == '') $errors[] = 'First name is required!';
    if($last_name == '') $errors[] = 'Last name is required!';
    if($email == '') $errors[] = 'Email is required!';
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email format is not valid!';

    // Only update if no validation errors
    if(empty($errors)) {
        $data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'is_active' => $is_active
        ];

        // Use Teacher class updateTeacher method
        if($teacherObj->updateTeacher($teacher_id, $data)) {
            header('Location: teacher_list.php?success=Teacher updated successfully!');
            exit();
        } else {
            $errors[] = 'Error updating teacher!';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edu Team - Edit Teacher</title>
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

        /* Red error box */
        .error-box { background: #fce4ec; color: #c62828; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
        .error-box ul { margin-left: 16px; }
    </style>
</head>
<body>
    <div class="navbar">
        <span>Edu Team - Student Record System</span>
        <div class="navbar-right">
            <a href="dashboard.php">Dashboard</a>
            <a href="teacher_list.php">Teachers</a>
            <a href="../isha/logout.php">Logout</a>
            <div class="avatar">SS</div>
        </div>
    </div>

    <div class="content">
        <div class="card">
            <h1>Edit Teacher</h1>
            <p class="subtitle">Developer: Sita Subedi | SRS-83</p>

            <?php
            if(!empty($errors)) {
                echo '<div class="error-box"><ul>';
                foreach($errors as $error) echo '<li>' . $error . '</li>';
                echo '</ul></div>';
            }
            ?>

            <!-- Form pre-filled with existing teacher data -->
            <form method="POST">
                <label>First Name:</label>
                <input type="text" name="first_name"
                    value="<?php echo $teacher['first_name']; ?>" required>

                <label>Last Name:</label>
                <input type="text" name="last_name"
                    value="<?php echo $teacher['last_name']; ?>" required>

                <label>Email:</label>
                <input type="email" name="email"
                    value="<?php echo $teacher['email']; ?>" required>

                <!-- PHP echo selected on matching option -->
                <label>Status:</label>
                <select name="is_active">
                    <option value="1" <?php if($teacher['is_active'] == 1) echo 'selected'; ?>>Active</option>
                    <option value="0" <?php if($teacher['is_active'] == 0) echo 'selected'; ?>>Inactive</option>
                </select>

                <button type="submit" class="update-btn">Update Teacher</button>
                <a href="teacher_list.php" class="back-link">&larr; Back to All Teachers</a>
            </form>
        </div>
    </div>
</body>
</html>