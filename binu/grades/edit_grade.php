<?php
// edit_grade.php - Update a grade record
require_once '../../db.php';
header('Content-Type: application/json');

$grade_id   = $_POST['grade_id']   ?? '';
$mid_term   = $_POST['mid_term']   ?? '';
$final_term = $_POST['final_term'] ?? '';

if (!$grade_id || $mid_term === '' || $final_term === '') {
    echo json_encode(['error' => 'All fields are required.']);
    exit;
}

$total_grade = $mid_term + $final_term;
$percentage  = ($total_grade / 200) * 100;
$is_passed   = $percentage >= 50 ? 1 : 0;

$stmt = $conn->prepare("UPDATE grade SET mid_term=?, final_term=?, total_grade=?, percentage=?, is_passed=? WHERE grade_id=?");
$stmt->bind_param('ddddii', $mid_term, $final_term, $total_grade, $percentage, $is_passed, $grade_id);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Grade updated successfully.']);
} else {
    echo json_encode(['error' => 'Failed to update grade.']);
}
?>