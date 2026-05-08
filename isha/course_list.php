<?php
// course_list.php - Course List Page
// Developer: Isha | Module: User Authentication
// Project: Edu Team - Student Record System

session_start(); // Start session
include '../db.php'; // Include database connection
include 'Course.php'; // Include Course middle layer class

// Create Course object
$courseObj = new Course($conn);

// Get search and filter values from URL
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';

// Use Course class to get all courses
$result = $courseObj->getAllCourses($search, $filter);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edu Team - Course Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f0f5; }
        .navbar { background: #4a235a; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-left { color: white; font-size: 16px; font-weight: 500; }
        .navbar-right { display: flex; align-items: center; gap: 20px; }
        .navbar-right a { color: #ddd; font-size: 13px; text-decoration: none; }
        .avatar { width: 30px; height: 30px; border-radius: 50%; background: #6b3580; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 500; }
        .content { padding: 24px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 500; color: #333; margin-bottom: 4px; }
        .page-subtitle { font-size: 13px; color: #999; }
        .add-btn { background: #4a235a; color: white; padding: 10px 18px; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none; }
        .search-form { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; width: 250px; outline: none; }
        .filter-select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; outline: none; }
        .search-btn { padding: 8px 16px; background: #4a235a; color: white; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none; }
        .card { background: white; border: 1px solid #eee; border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead { background: #f9f9f9; }
        th { text-align: left; padding: 10px 16px; color: #999; font-weight: 500; }
        td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; }
        .badge-active { background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 10px; font-size: 11px; }
        .badge-inactive { background: #fce4ec; color: #c62828; padding: 3px 10px; border-radius: 10px; font-size: 11px; }
        .edit-btn { background: #e3f2fd; color: #1565c0; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; margin-right: 4px; text-decoration: none; }
        .delete-btn { background: #fce4ec; color: #c62828; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; text-decoration: none; }
        .card-footer { padding: 12px 16px; border-top: 1px solid #eee; }
        .card-footer p { font-size: 12px; color: #999; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; }
        .error { background: #fce4ec; color: #c62828; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="navbar">
        <span class="navbar-left">Edu Team - Student Record System</span>
        <div class="navbar-right">
            <a href="../sita/dashboard.php">Dashboard</a>
            <a href="../deepa/student_list.php">Students</a>
            <a href="course_list.php">Courses</a>
            <a href="logout.php">Logout</a>
            <div class="avatar">ID</div>
        </div>
    </div>

    <div class="content">
        <?php
        if(isset($_GET['success'])) echo '<div class="success">' . $_GET['success'] . '</div>';
        if(isset($_GET['error'])) echo '<div class="error">' . $_GET['error'] . '</div>';
        ?>

        <div class="header">
            <div>
                <p class="page-title">Course Management</p>
                <p class="page-subtitle">Developer: Isha | SRS-86</p>
            </div>
            <a href="add_course.php" class="add-btn">+ Add New Course</a>
        </div>

        <form method="GET" class="search-form">
            <input type="text" name="search" class="search-input"
                placeholder="Search by course name or code..."
                value="<?php echo $search; ?>">
            <select name="filter" class="filter-select">
                <option value="">All Courses</option>
                <option value="active" <?php if($filter=='active') echo 'selected'; ?>>Active</option>
                <option value="inactive" <?php if($filter=='inactive') echo 'selected'; ?>>Inactive</option>
            </select>
            <button type="submit" class="search-btn">Search</button>
            <a href="course_list.php" class="search-btn">Clear</a>
        </form>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Course Name</th><th>Course Code</th>
                        <th>Teacher ID</th><th>Start Date</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $row['course_id'] . '</td>';
                            echo '<td>' . $row['course_name'] . '</td>';
                            echo '<td>' . $row['course_code'] . '</td>';
                            echo '<td>' . $row['teacher_id'] . '</td>';
                            echo '<td>' . $row['start_date'] . '</td>';
                            echo $row['is_active'] == 1
                                ? '<td><span class="badge-active">Active</span></td>'
                                : '<td><span class="badge-inactive">Inactive</span></td>';
                            echo '<td>';
                            echo '<a href="edit_course.php?id=' . $row['course_id'] . '" class="edit-btn">Edit</a>';
                            echo '<a href="delete_course.php?id=' . $row['course_id'] . '" class="delete-btn" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                            echo '</td></tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7" style="text-align:center;padding:20px;color:#999;">No courses found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div class="card-footer">
                <p>Total: <?php echo $courseObj->countAllCourses(); ?> courses</p>
            </div>
        </div>
    </div>
</body>
</html>