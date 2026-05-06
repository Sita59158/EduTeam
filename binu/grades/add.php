<?php
// Connect to database
require_once '../../db.php';
header('Content-Type: application/json');

// Get POST data from form
$student_id = $_POST['student_id'] ?? '';
$course_id  = $_POST['course_id']  ?? '';
$mid_term   = $_POST['mid_term']   ?? '';
$final_term = $_POST['final_term'] ?? '';

// Validate all fields are provided
if (!$student_id || !$course_id || $mid_term === '' || $final_term === '') {
    echo json_encode(['error' => 'All fields are required.']);
    exit;
}

// Validate scores between 0 and 100
if (!is_numeric($mid_term) || $mid_term < 0 || $mid_term > 100) {
    echo json_encode(['error' => 'Mid-term must be between 0 and 100.']);
    exit;
}

if (!is_numeric($final_term) || $final_term < 0 || $final_term > 100) {
    echo json_encode(['error' => 'Final term must be between 0 and 100.']);
    exit;
}

// Calculate total, percentage and pass/fail
$total_grade = $mid_term + $final_term;
$percentage  = ($total_grade / 200) * 100;
$is_passed   = $percentage >= 50 ? 1 : 0;

// Insert into database
$stmt = $conn->prepare("INSERT INTO grade (student_id, course_id, mid_term, final_term, total_grade, percentage, is_passed) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('isddddi', $student_id, $course_id, $mid_term, $final_term, $total_grade, $percentage, $is_passed);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Grade added successfully.']);
} else {
    echo json_encode(['error' => 'Failed to add grade.']);
}
?>