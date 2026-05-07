<?php
// teacher_list.php - Teacher List Page
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

// Get search and filter values from URL
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : '';

// Use Teacher class to get all teachers
$result = $teacherObj->getAllTeachers($search, $filter);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edu Team - Teacher Management</title>
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

        /* Circle avatar for user initials */
        .avatar { width: 30px; height: 30px; border-radius: 50%; background: #6b3580; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 500; }
        .content { padding: 24px; }

        /* Header - title left, add button right */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 500; color: #333; margin-bottom: 4px; }
        .page-subtitle { font-size: 13px; color: #999; }
        .add-btn { background: #4a235a; color: white; padding: 10px 18px; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none; }

        /* Search form */
        .search-form { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; width: 250px; outline: none; }
        .filter-select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; outline: none; }
        .search-btn { padding: 8px 16px; background: #4a235a; color: white; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none; }

        /* White card for table */
        .card { background: white; border: 1px solid #eee; border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead { background: #f9f9f9; }
        th { text-align: left; padding: 10px 16px; color: #999; font-weight: 500; }
        td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; }

        /* Green badge active, red badge inactive */
        .badge-active { background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 10px; font-size: 11px; }
        .badge-inactive { background: #fce4ec; color: #c62828; padding: 3px 10px; border-radius: 10px; font-size: 11px; }

        /* Blue edit, red delete buttons */
        .edit-btn { background: #e3f2fd; color: #1565c0; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; margin-right: 4px; text-decoration: none; }
        .delete-btn { background: #fce4ec; color: #c62828; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; text-decoration: none; }
        .card-footer { padding: 12px 16px; border-top: 1px solid #eee; }
        .card-footer p { font-size: 12px; color: #999; }

        /* Success and error message boxes */
        .success { background: #e8f5e9; color: #2e7d32; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; }
        .error { background: #fce4ec; color: #c62828; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; }
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
            <div class="avatar">SS</div>
        </div>
    </div>

    <div class="content">
        <?php
        // Show success or error messages
        if(isset($_GET['success'])) echo '<div class="success">' . $_GET['success'] . '</div>';
        if(isset($_GET['error'])) echo '<div class="error">' . $_GET['error'] . '</div>';
        ?>

        <div class="header">
            <div>
                <p class="page-title">Teacher Management</p>
                <p class="page-subtitle">Developer: Sita Subedi | SRS-83</p>
            </div>
            <a href="add_teacher.php" class="add-btn">+ Add New Teacher</a>
        </div>

        <!-- Search and filter form -->
        <form method="GET" class="search-form">
            <input type="text" name="search" class="search-input"
                placeholder="Search by teacher name..."
                value="<?php echo $search; ?>">

            <!-- Filter by active or inactive status -->
            <select name="filter" class="filter-select">
                <option value="">All Teachers</option>
                <option value="active" <?php if($filter=='active') echo 'selected'; ?>>Active</option>
                <option value="inactive" <?php if($filter=='inactive') echo 'selected'; ?>>Inactive</option>
            </select>
            <button type="submit" class="search-btn">Search</button>
            <a href="teacher_list.php" class="search-btn">Clear</a>
        </form>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>First Name</th><th>Last Name</th>
                        <th>Email</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through and display each teacher
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $row['teacher_id'] . '</td>';
                            echo '<td>' . $row['first_name'] . '</td>';
                            echo '<td>' . $row['last_name'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';

                            // Show green or red badge
                            echo $row['is_active'] == 1
                                ? '<td><span class="badge-active">Active</span></td>'
                                : '<td><span class="badge-inactive">Inactive</span></td>';

                            // Edit and delete buttons
                            echo '<td>';
                            echo '<a href="edit_teacher.php?id=' . $row['teacher_id'] . '" class="edit-btn">Edit</a>';
                            echo '<a href="delete_teacher.php?id=' . $row['teacher_id'] . '" class="delete-btn" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                            echo '</td></tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" style="text-align:center;padding:20px;color:#999;">No teachers found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div class="card-footer">
                <p>Total: <?php echo $teacherObj->countAllTeachers(); ?> teachers</p>
            </div>
        </div>
    </div>
</body>
</html>