<?php
require_once '../db.php';

// ADD attendance record
if (isset($_POST['add'])) {
    $student_id         = $_POST['student_id'];
    $teacher_id         = $_POST['teacher_id'];
    $status             = $_POST['status'];
    $attendance_date    = $_POST['attendance_date'];

    // Calculate absence percentage
    $total_query = "SELECT COUNT(*) as total FROM attendance WHERE student_id='$student_id'";
    $total_result = mysqli_query($conn, $total_query);
    $total_row = mysqli_fetch_assoc($total_result);
    $total = $total_row['total'] + 1;

    $absent_query = "SELECT COUNT(*) as absents FROM attendance WHERE student_id='$student_id' AND status='Absent'";
    $absent_result = mysqli_query($conn, $absent_query);
    $absent_row = mysqli_fetch_assoc($absent_result);
    $absents = $absent_row['absents'];
    if ($status == 'Absent') $absents++;

    $absence_percentage = ($absents / $total) * 100;
    $warning = ($absence_percentage > 20) ? 1 : 0;

    $sql = "INSERT INTO attendance (student_id, teacher_id, status, attendance_date, absence_percentage, warning) 
            VALUES ('$student_id', '$teacher_id', '$status', '$attendance_date', '$absence_percentage', '$warning')";
    mysqli_query($conn, $sql);
    header("Location: attendance_list.php");
}

// EDIT attendance record
if (isset($_POST['edit'])) {
    $attendance_id      = $_POST['attendance_id'];
    $student_id         = $_POST['student_id'];
    $teacher_id         = $_POST['teacher_id'];
    $status             = $_POST['status'];
    $attendance_date    = $_POST['attendance_date'];

    // Recalculate absence percentage
    $total_query = "SELECT COUNT(*) as total FROM attendance WHERE student_id='$student_id'";
    $total_result = mysqli_query($conn, $total_query);
    $total_row = mysqli_fetch_assoc($total_result);
    $total = $total_row['total'];

    $absent_query = "SELECT COUNT(*) as absents FROM attendance WHERE student_id='$student_id' AND status='Absent'";
    $absent_result = mysqli_query($conn, $absent_query);
    $absent_row = mysqli_fetch_assoc($absent_result);
    $absents = $absent_row['absents'];

    $absence_percentage = $total > 0 ? ($absents / $total) * 100 : 0;
    $warning = ($absence_percentage > 20) ? 1 : 0;

    $sql = "UPDATE attendance SET 
            student_id='$student_id', teacher_id='$teacher_id',
            status='$status', attendance_date='$attendance_date',
            absence_percentage='$absence_percentage', warning='$warning'
            WHERE attendance_id='$attendance_id'";
    mysqli_query($conn, $sql);
    header("Location: attendance_list.php");
}

// DELETE attendance record
if (isset($_GET['delete'])) {
    $attendance_id = $_GET['delete'];
    $sql = "DELETE FROM attendance WHERE attendance_id='$attendance_id'";
    mysqli_query($conn, $sql);
    header("Location: attendance_list.php");
}
?>