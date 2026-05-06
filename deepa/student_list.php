<?php
// student_list.php - Student List Page
// Developer: Deepa Thapa | Module: Student Profile Management
// Project: Edu Team - Student Record System

session_start(); // Start session
include '../db.php'; // Include database connection
include 'Student.php'; // Include Student middle layer class

// Create Student object
$studentObj = new Student($conn);

// Get search and filter values from URL
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : '';

// Use Student class method to get students
$result = $studentObj->getAllStudents($search, $filter);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edu Team - Student Record System</title>
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

        /* Header - title left, add button right */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 500; color: #333; margin-bottom: 4px; }
        .page-subtitle { font-size: 13px; color: #999; }
        .add-btn { background: #4a235a; color: white; padding: 10px 18px; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none; }

        /* Search form - flex row layout */
        .search-form { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; width: 250px; outline: none; }
        .filter-select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; outline: none; }
        .search-btn { padding: 8px 16px; background: #4a235a; color: white; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none; }

        /* White card for student table */
        .card { background: white; border: 1px solid #eee; border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead { background: #f9f9f9; }
        th { text-align: left; padding: 10px 16px; color: #999; font-weight: 500; }
        td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; }

        /* Green badge for active, red for inactive */
        .badge-active { background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 10px; font-size: 11px; }
        .badge-inactive { background: #fce4ec; color: #c62828; padding: 3px 10px; border-radius: 10px; font-size: 11px; }

        /* Blue edit button, red delete button */
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
            <a href="../sita/dashboard.php">Dashboard</a>
            <a href="student_list.php">Students</a>
            <a href="../isha/course_list.php">Courses</a>
            <a href="../isha/logout.php">Logout</a>
            <div class="avatar">DT</div>
        </div>
    </div>

    <div class="content">
        <?php
        // Show success or error messages passed via URL
        if(isset($_GET['success'])) echo '<div class="success">' . $_GET['success'] . '</div>';
        if(isset($_GET['error'])) echo '<div class="error">' . $_GET['error'] . '</div>';
        ?>

        <div class="header">
            <div>
                <p class="page-title">Student Profile Management</p>
                <p class="page-subtitle">Developer: Deepa Thapa | SRS-84</p>
            </div>
            <a href="add_student.php" class="add-btn">+ Add New Student</a>
        </div>

        <!-- Search and filter form - GET keeps values in URL -->
        <form method="GET" class="search-form">
            <input type="text" name="search" class="search-input"
                placeholder="Search by student name..."
                value="<?php echo $search; ?>">

            <!-- Filter dropdown - PHP keeps selected option -->
            <select name="filter" class="filter-select">
                <option value="">All Students</option>
                <option value="active" <?php if($filter=='active') echo 'selected'; ?>>Active</option>
                <option value="inactive" <?php if($filter=='inactive') echo 'selected'; ?>>Inactive</option>
            </select>
            <button type="submit" class="search-btn">Search</button>
            <a href="student_list.php" class="search-btn">Clear</a>
        </form>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Full Name</th><th>Email</th>
                        <th>Enrolled Date</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through results using Student class data
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $row['student_id'] . '</td>';
                            echo '<td>' . $row['full_name'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>' . $row['enrolled_date'] . '</td>';

                            // Show green or red badge based on is_active
                            echo $row['is_active'] == 1
                                ? '<td><span class="badge-active">Active</span></td>'
                                : '<td><span class="badge-inactive">Inactive</span></td>';

                            // Edit and delete buttons pass student ID in URL
                            echo '<td>';
                            echo '<a href="edit_student.php?id=' . $row['student_id'] . '" class="edit-btn">Edit</a>';
                            echo '<a href="delete_student.php?id=' . $row['student_id'] . '" class="delete-btn" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                            echo '</td></tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" style="text-align:center;padding:20px;color:#999;">No students found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div class="card-footer">
                <!-- Use Student class to count total students -->
                <p>Total: <?php echo $studentObj->countAllStudents(); ?> students</p>
            </div>
        </div>
    </div>
</body>
</html>