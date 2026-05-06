<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$grade_id = $_POST['grade_id'] ?? '';

if (!$grade_id) {
    echo json_encode(["error" => "Grade ID is required."]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM grade WHERE grade_id = ?");
$stmt->bind_param("i", $grade_id);

if ($stmt->execute()) {
    echo json_encode(["success" => "Grade deleted."]);
} else {
    echo json_encode(["error" => "Failed to delete grade."]);
}
?>