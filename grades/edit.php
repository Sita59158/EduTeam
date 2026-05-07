<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$data       = json_decode(file_get_contents("php://input"), true);

$grade_id   = $data['grade_id'];
$student_id = $data['student_id'];
$course_id  = $data['course_id'];
$mid_term   = $data['mid_term'];
$final_term = $data['final_term'];

// Validation
if ($mid_term < 0 || $mid_term > 100 || $final_term < 0 || $final_term > 100) {
    echo json_encode(["error" => "Scores must be between 0 and 100"]);
    exit;
}

// Auto-calculate
$total_grade = $mid_term + $final_term;
$percentage  = ($total_grade / 200) * 100;
$is_passed   = $percentage >= 50 ? 1 : 0;

$stmt = $conn->prepare("UPDATE grade SET student_id=?, course_id=?, mid_term=?, final_term=?, total_grade=?, percentage=?, is_passed=? WHERE grade_id=?");
$stmt->bind_param("iiddddii", $student_id, $course_id, $mid_term, $final_term, $total_grade, $percentage, $is_passed, $grade_id);

if ($stmt->execute()) {
    echo json_encode(["success" => "Grade updated successfully"]);
} else {
    echo json_encode(["error" => "Failed to update grade"]);
}
?>