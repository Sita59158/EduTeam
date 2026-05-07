<?php
// grade_list.php - Fetch all grade records
require_once '../../db.php';
header('Content-Type: application/json');

$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : '';

if ($student_id) {
    // Get grades for specific student
    $student_id = mysqli_real_escape_string($conn, $student_id);
    $result = mysqli_query($conn, "SELECT * FROM grade WHERE student_id = '$student_id' ORDER BY grade_id DESC");
} else {
    // Get all grades
    $result = mysqli_query($conn, "SELECT * FROM grade ORDER BY grade_id DESC");
}

$grades = [];
while ($row = mysqli_fetch_assoc($result)) {
    $grades[] = $row;
}
echo json_encode($grades);
?>