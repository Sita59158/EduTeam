<?php
require_once '../../db.php';
header('Content-Type: application/json');

$student_id = $_GET['student_id'] ?? '';

if ($student_id) {
    $stmt = $conn->prepare("SELECT * FROM grade WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM grade");
}

$grades = [];
while ($row = $result->fetch_assoc()) {
    $grades[] = $row;
}

echo json_encode($grades);
?>