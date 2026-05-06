<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$grade_id = $_GET['grade_id'] ?? '';

if (!$grade_id) {
    echo json_encode(["error" => "Grade ID is required."]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM grade WHERE grade_id = ?");
$stmt->bind_param("i", $grade_id);
$stmt->execute();
$result = $stmt->get_result();
$grade  = $result->fetch_assoc();

echo json_encode($grade);
?>