<?php
// ============================================================
// FILE: teacher_dashboard.php
// MODULE: Grade & Result Tracking
// PROJECT: EduTeam - Student Record System
// DEVELOPER: Binu Karki
// LAYER: Middle Layer (Business Logic) + Presentation Layer
// DESCRIPTION: Teacher dashboard - Add, Edit, Delete, Search,
//              Filter grades and view Pass/Fail chart analytics
// ============================================================

// MIDDLE LAYER: Start session
session_start();

// Security check: only teachers allowed
// Redirect to login if not authenticated as teacher
if (!isset($_SESSION['grade_user']) || $_SESSION['grade_role'] !== 'teacher') {
    header("Location: grade_login.php");
    exit();
}

// DATA LAYER: Include shared database connection
// Uses $conn (procedural mysqli) from shared db.php
require_once '../../db.php';

$success = '';
$error   = '';

// ============================================================
// MIDDLE LAYER: ADD GRADE
// Processes form when teacher submits a new grade
// ============================================================
if (isset($_POST['action']) && $_POST['action'] === 'add') {

    // Sanitize inputs
    $student_id = intval($_POST['student_id']);      // Cast to int
    $course_id  = trim($_POST['course_id']);          // Remove spaces
    $mid_term   = floatval($_POST['mid_term']);       // Cast to float
    $final_term = floatval($_POST['final_term']);     // Cast to float

    // MIDDLE LAYER: Validate all inputs
    if ($student_id <= 0 || empty($course_id) || $mid_term < 0 || $mid_term > 100 || $final_term < 0 || $final_term > 100) {
        $error = "Please fill all fields correctly. Marks must be between 0 and 100.";
    } else {
        // Calculate total, percentage and pass/fail
        $total_grade = $mid_term + $final_term;
        $percentage  = ($total_grade / 200) * 100;
        $is_passed   = ($percentage >= 50) ? 1 : 0;

        // DATA LAYER: Insert new grade using prepared statement
        $stmt = mysqli_prepare($conn,
            "INSERT INTO grade (student_id, course_id, mid_term, final_term, total_grade, percentage, is_passed)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        // Bind: i=int, s=string, d=double
        mysqli_stmt_bind_param($stmt, "isddddi",
            $student_id, $course_id, $mid_term, $final_term, $total_grade, $percentage, $is_passed
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Grade added successfully!";
        } else {
            $error = "Failed to add grade. Please try again.";
        }
        mysqli_stmt_close($stmt); // Free resources
    }
}

// ============================================================
// MIDDLE LAYER: EDIT GRADE
// Updates an existing grade record in the database
// ============================================================
if (isset($_POST['action']) && $_POST['action'] === 'edit') {

    // Sanitize inputs
    $grade_id   = intval($_POST['grade_id']);
    $student_id = intval($_POST['student_id']);
    $course_id  = trim($_POST['course_id']);
    $mid_term   = floatval($_POST['mid_term']);
    $final_term = floatval($_POST['final_term']);

    // Validate inputs
    if ($grade_id <= 0 || $student_id <= 0 || empty($course_id) || $mid_term < 0 || $final_term < 0) {
        $error = "Invalid data. Please check your inputs.";
    } else {
        // Recalculate values
        $total_grade = $mid_term + $final_term;
        $percentage  = ($total_grade / 200) * 100;
        $is_passed   = ($percentage >= 50) ? 1 : 0;

        // DATA LAYER: Update grade record
        $stmt = mysqli_prepare($conn,
            "UPDATE grade SET student_id=?, course_id=?, mid_term=?, final_term=?,
             total_grade=?, percentage=?, is_passed=? WHERE grade_id=?"
        );

        mysqli_stmt_bind_param($stmt, "isddddii",
            $student_id, $course_id, $mid_term, $final_term, $total_grade, $percentage, $is_passed, $grade_id
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Grade updated successfully!";
        } else {
            $error = "Failed to update grade.";
        }
        mysqli_stmt_close($stmt);
    }
}

// ============================================================
// MIDDLE LAYER: DELETE GRADE
// Deletes a grade record by grade_id from URL
// ============================================================
if (isset($_GET['delete'])) {

    $grade_id = intval($_GET['delete']); // Sanitize ID

    // DATA LAYER: Delete prepared statement
    $stmt = mysqli_prepare($conn, "DELETE FROM grade WHERE grade_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $grade_id);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Grade deleted successfully!";
    } else {
        $error = "Failed to delete grade.";
    }
    mysqli_stmt_close($stmt);
}

// ============================================================
// MIDDLE LAYER: SEARCH & FILTER
// Dynamically builds query based on user search/filter input
// ============================================================
$search        = isset($_GET['search'])        ? trim($_GET['search'])        : '';
$filter_course = isset($_GET['filter_course']) ? trim($_GET['filter_course']) : '';

// Base SQL query
$query  = "SELECT * FROM grade WHERE 1=1";
$params = [];
$types  = '';

// Add search condition if provided
if (!empty($search)) {
    $query   .= " AND student_id LIKE ?";
    $params[] = "%$search%";
    $types   .= 's';
}

// Add course filter condition if selected
if (!empty($filter_course)) {
    $query   .= " AND course_id = ?";
    $params[] = $filter_course;
    $types   .= 's';
}

$query .= " ORDER BY grade_id DESC";

// DATA LAYER: Execute dynamic search query
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$grades = mysqli_fetch_all($result, MYSQLI_ASSOC); // All rows as array
mysqli_stmt_close($stmt);

// ============================================================
// DATA LAYER: CHART AGGREGATE QUERY
// Counts passed/failed for Mid Term, Final Term, Overall
// ============================================================
$chart_result = mysqli_query($conn,
    "SELECT
        SUM(CASE WHEN mid_term >= 50 THEN 1 ELSE 0 END)   AS mid_passed,
        SUM(CASE WHEN mid_term < 50 THEN 1 ELSE 0 END)    AS mid_failed,
        SUM(CASE WHEN final_term >= 50 THEN 1 ELSE 0 END) AS final_passed,
        SUM(CASE WHEN final_term < 50 THEN 1 ELSE 0 END)  AS final_failed,
        SUM(CASE WHEN is_passed = 1 THEN 1 ELSE 0 END)    AS overall_passed,
        SUM(CASE WHEN is_passed = 0 THEN 1 ELSE 0 END)    AS overall_failed
     FROM grade"
);
$chart_data = mysqli_fetch_assoc($chart_result);

// DATA LAYER: Fetch distinct courses for filter dropdown
$courses_result = mysqli_query($conn, "SELECT DISTINCT course_id FROM grade ORDER BY course_id");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

// MIDDLE LAYER: Fetch grade record if edit button clicked
$edit_grade = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);

    // DATA LAYER: Fetch single grade by ID
    $stmt = mysqli_prepare($conn, "SELECT * FROM grade WHERE grade_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $edit_id);
    mysqli_stmt_execute($stmt);
    $edit_result = mysqli_stmt_get_result($stmt);
    $edit_grade  = mysqli_fetch_assoc($edit_result);
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard | EduTeam Grades</title>

    <!-- PRESENTATION LAYER: Fonts, Icons, Chart.js -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ============================================================
           PRESENTATION LAYER: CSS - EduTeam Purple Theme
        ============================================================ */
        :root {
            --primary:    #4a235a;
            --hover:      #6b3580;
            --bg:         #f0f0f5;
            --white:      #ffffff;
            --success:    #1e8449;
            --error:      #c0392b;
            --text:       #2c2c2c;
            --light-text: #7a7a9d;
            --border:     #e0e0ee;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); }

        /* NAVBAR */
        .navbar {
            background: var(--primary);
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(74,35,90,0.3);
            position: sticky; top: 0; z-index: 100;
        }
        .navbar-brand {
            color: white; font-family: 'Playfair Display', serif;
            font-size: 1.4rem; text-decoration: none;
            display: flex; align-items: center; gap: 10px;
        }
        .navbar-right { display: flex; align-items: center; gap: 15px; }
        .navbar-user  { color: rgba(255,255,255,0.85); font-size: 0.9rem; }
        .btn-logout {
            background: rgba(255,255,255,0.15); color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 7px 16px; border-radius: 8px;
            font-family: 'DM Sans', sans-serif; font-size: 0.85rem;
            cursor: pointer; text-decoration: none; transition: all 0.3s;
            display: flex; align-items: center; gap: 6px;
        }
        .btn-logout:hover { background: rgba(255,255,255,0.25); }

        /* CONTAINER */
        .container { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }

        /* PAGE TITLE */
        .page-title { margin-bottom: 25px; }
        .page-title h1 { font-family: 'Playfair Display', serif; font-size: 1.9rem; color: var(--primary); }
        .page-title p  { color: var(--light-text); font-size: 0.9rem; margin-top: 4px; }

        /* ALERTS */
        .alert { padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #eafaf1; border-left: 4px solid var(--success); color: var(--success); }
        .alert-error   { background: #fdecea; border-left: 4px solid var(--error);   color: var(--error); }

        /* STATS GRID */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .stat-card  { background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 12px rgba(74,35,90,0.08); border-top: 4px solid var(--primary); }
        .stat-num   { font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--primary); font-weight: 700; }
        .stat-label { font-size: 0.8rem; color: var(--light-text); margin-top: 4px; }

        /* GRID 2 COLUMNS */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }

        /* CARDS */
        .card      { background: white; border-radius: 14px; padding: 25px; box-shadow: 0 2px 12px rgba(74,35,90,0.08); }
        .card-full { background: white; border-radius: 14px; padding: 25px; box-shadow: 0 2px 12px rgba(74,35,90,0.08); margin-bottom: 25px; }
        .card-title { font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--primary); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }

        /* FORM */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 500; color: var(--text); margin-bottom: 6px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .form-control { width: 100%; padding: 10px 13px; border: 2px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; outline: none; transition: border-color 0.3s; background: #fafafa; }
        .form-control:focus { border-color: var(--primary); background: white; }

        /* BUTTONS */
        .btn { padding: 10px 20px; border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; font-weight: 500; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 7px; text-decoration: none; }
        .btn-primary   { background: var(--primary); color: white; width: 100%; justify-content: center; margin-top: 5px; }
        .btn-primary:hover { background: var(--hover); box-shadow: 0 4px 15px rgba(74,35,90,0.3); }
        .btn-secondary { background: var(--bg); color: var(--text); width: 100%; justify-content: center; margin-top: 8px; }
        .btn-secondary:hover { background: #e0e0ee; }
        .btn-edit   { background: #eaf0fb; color: #2563eb; padding: 6px 12px; font-size: 0.8rem; }
        .btn-edit:hover   { background: #dce8f8; }
        .btn-delete { background: #fdecea; color: var(--error); padding: 6px 12px; font-size: 0.8rem; }
        .btn-delete:hover { background: #fbd5d2; }

        /* SEARCH BAR */
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .search-bar input, .search-bar select { padding: 10px 13px; border: 2px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; outline: none; background: white; transition: border-color 0.3s; }
        .search-bar input:focus, .search-bar select:focus { border-color: var(--primary); }
        .search-bar input { flex: 1; min-width: 180px; }
        .btn-search { background: var(--primary); color: white; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; display: flex; align-items: center; gap: 6px; transition: background 0.3s; }
        .btn-search:hover { background: var(--hover); }
        .btn-clear  { background: #f0f0f5; color: var(--text); border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; text-decoration: none; display: flex; align-items: center; gap: 6px; }
        .btn-clear:hover { background: #e0e0ee; }

        /* TABLE */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
        thead { background: var(--primary); color: white; }
        thead th { padding: 12px 15px; text-align: left; font-weight: 500; font-size: 0.85rem; }
        tbody tr { border-bottom: 1px solid var(--border); transition: background 0.2s; }
        tbody tr:hover { background: #f7f4fa; }
        tbody td { padding: 11px 15px; }
        .badge      { padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 500; }
        .badge-pass { background: #eafaf1; color: #1e8449; }
        .badge-fail { background: #fdecea; color: var(--error); }
        .no-data    { text-align: center; padding: 30px; color: var(--light-text); }

        /* CHART */
        .chart-container { position: relative; height: 280px; }

        /* FOOTER */
        footer { text-align: center; padding: 20px; color: var(--light-text); font-size: 0.8rem; margin-top: 20px; }
    </style>
</head>
<body>

<!-- PRESENTATION LAYER: Navbar -->
<nav class="navbar">
    <a href="#" class="navbar-brand">
        <i class="fas fa-graduation-cap"></i> EduTeam — Grades
    </a>
    <div class="navbar-right">
        <span class="navbar-user">
            <i class="fas fa-chalkboard-teacher"></i>
            <?= htmlspecialchars($_SESSION['grade_user']) ?>
        </span>
        <a href="grade_logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>

<div class="container">

    <!-- Page Title -->
    <div class="page-title">
        <h1><i class="fas fa-chart-bar" style="font-size:1.5rem; margin-right:8px;"></i>Teacher Dashboard</h1>
        <p>Manage all student grades — add, edit, delete, search and view analytics</p>
    </div>

    <!-- PRESENTATION LAYER: Success/Error Alerts -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- PRESENTATION LAYER: Stats Summary Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-num"><?= count($grades) ?></div>
            <div class="stat-label">Records Shown</div>
        </div>
        <div class="stat-card">
            <div class="stat-num"><?= $chart_data['overall_passed'] ?? 0 ?></div>
            <div class="stat-label">Overall Passed</div>
        </div>
        <div class="stat-card">
            <div class="stat-num"><?= $chart_data['overall_failed'] ?? 0 ?></div>
            <div class="stat-label">Overall Failed</div>
        </div>
        <div class="stat-card">
            <div class="stat-num"><?= count($courses) ?></div>
            <div class="stat-label">Courses</div>
        </div>
    </div>

    <!-- PRESENTATION LAYER: Add/Edit Form + Chart -->
    <div class="grid-2">

        <!-- Add / Edit Grade Form -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-<?= $edit_grade ? 'edit' : 'plus-circle' ?>"></i>
                <?= $edit_grade ? 'Edit Grade' : 'Add New Grade' ?>
            </div>

            <!-- POST to same page; hidden action field tells PHP what to do -->
            <form method="POST" action="">
                <input type="hidden" name="action" value="<?= $edit_grade ? 'edit' : 'add' ?>">
                <?php if ($edit_grade): ?>
                    <input type="hidden" name="grade_id" value="<?= $edit_grade['grade_id'] ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label>Student ID</label>
                        <input type="number" name="student_id" class="form-control"
                               placeholder="e.g. 101"
                               value="<?= $edit_grade ? $edit_grade['student_id'] : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Course ID</label>
                        <input type="text" name="course_id" class="form-control"
                               placeholder="e.g. MATH"
                               value="<?= $edit_grade ? $edit_grade['course_id'] : '' ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Mid Term (out of 100)</label>
                        <input type="number" name="mid_term" class="form-control"
                               step="0.01" min="0" max="100" placeholder="0 - 100"
                               value="<?= $edit_grade ? $edit_grade['mid_term'] : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Final Term (out of 100)</label>
                        <input type="number" name="final_term" class="form-control"
                               step="0.01" min="0" max="100" placeholder="0 - 100"
                               value="<?= $edit_grade ? $edit_grade['final_term'] : '' ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-<?= $edit_grade ? 'save' : 'plus' ?>"></i>
                    <?= $edit_grade ? 'Update Grade' : 'Add Grade' ?>
                </button>

                <?php if ($edit_grade): ?>
                    <a href="teacher_dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel Edit
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Pass/Fail Bar Chart -->
        <div class="card">
            <div class="card-title"><i class="fas fa-chart-pie"></i> Pass / Fail Overview</div>
            <div class="chart-container">
                <canvas id="gradeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- PRESENTATION LAYER: Grade List Table -->
    <div class="card-full">
        <div class="card-title"><i class="fas fa-list"></i> All Student Grades</div>

        <!-- Search & Filter Form (GET method) -->
        <form method="GET" action="">
            <div class="search-bar">
                <input type="text" name="search"
                       placeholder="Search by Student ID..."
                       value="<?= htmlspecialchars($search) ?>">
                <select name="filter_course">
                    <option value="">All Courses</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= htmlspecialchars($c['course_id']) ?>"
                            <?= $filter_course === $c['course_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['course_id']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="teacher_dashboard.php" class="btn-clear">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>

        <!-- Grades Table -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Course</th>
                        <th>Mid Term</th>
                        <th>Final Term</th>
                        <th>Total</th>
                        <th>Percentage</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grades)): ?>
                        <tr>
                            <td colspan="9" class="no-data">
                                <i class="fas fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                No grades found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($grades as $i => $g): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= htmlspecialchars($g['student_id']) ?></strong></td>
                                <td><?= htmlspecialchars($g['course_id']) ?></td>
                                <td><?= $g['mid_term'] ?></td>
                                <td><?= $g['final_term'] ?></td>
                                <td><?= $g['total_grade'] ?></td>
                                <td><?= $g['percentage'] ?>%</td>
                                <td>
                                    <span class="badge <?= $g['is_passed'] ? 'badge-pass' : 'badge-fail' ?>">
                                        <?= $g['is_passed'] ? '✓ Pass' : '✗ Fail' ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Edit: sends grade_id via GET to pre-fill form -->
                                    <a href="?edit=<?= $g['grade_id'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <!-- Delete: JS confirm before deleting -->
                                    <a href="?delete=<?= $g['grade_id'] ?>"
                                       class="btn btn-delete"
                                       onclick="return confirm('Are you sure you want to delete this grade?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<footer>&copy; 2026 EduTeam | Grade &amp; Result Tracking Module | Developed by Binu Karki</footer>

<!-- PRESENTATION LAYER: Chart.js Bar Chart Script -->
<script>
    const ctx = document.getElementById('gradeChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Mid Term', 'Final Term', 'Overall'],
            datasets: [
                {
                    label: 'Passed',
                    data: [
                        <?= $chart_data['mid_passed']     ?? 0 ?>,
                        <?= $chart_data['final_passed']   ?? 0 ?>,
                        <?= $chart_data['overall_passed'] ?? 0 ?>
                    ],
                    backgroundColor: '#1e8449',
                    borderRadius: 6
                },
                {
                    label: 'Failed',
                    data: [
                        <?= $chart_data['mid_failed']     ?? 0 ?>,
                        <?= $chart_data['final_failed']   ?? 0 ?>,
                        <?= $chart_data['overall_failed'] ?? 0 ?>
                    ],
                    backgroundColor: '#c0392b',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>

</body>
</html>
