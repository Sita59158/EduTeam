<?php
// Teacher.php - Teacher Class (Middle Layer)
// Developer: Sita Subedi
// Module: Teacher Dashboard
// Project: Edu Team - Student Record System

class Teacher {
    // Database connection variable
    private $conn;

    // Constructor - receives database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all teachers with optional search and filter
    public function getAllTeachers($search = '', $filter = '') {
        // Base query
        $query = "SELECT * FROM Teacher WHERE 1=1";

        // Add search by name
        if($search != '') {
            $query .= " AND (first_name LIKE '%$search%' 
                        OR last_name LIKE '%$search%')";
        }

        // Add filter by active status
        if($filter == 'active') {
            $query .= " AND is_active = 1";
        } elseif($filter == 'inactive') {
            $query .= " AND is_active = 0";
        }

        return mysqli_query($this->conn, $query);
    }

    // Get single teacher by ID
    public function getTeacher($id) {
        $query = "SELECT * FROM Teacher 
                  WHERE teacher_id = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Add new teacher to database
    public function addTeacher($data) {
        $query = "INSERT INTO Teacher 
        (first_name, last_name, email, 
        password, is_active) 
        VALUES (
        '{$data['first_name']}',
        '{$data['last_name']}',
        '{$data['email']}',
        MD5('{$data['password']}'),
        '{$data['is_active']}')";
        return mysqli_query($this->conn, $query);
    }

    // Update existing teacher in database
    public function updateTeacher($id, $data) {
        $query = "UPDATE Teacher SET 
        first_name = '{$data['first_name']}',
        last_name = '{$data['last_name']}',
        email = '{$data['email']}',
        is_active = '{$data['is_active']}'
        WHERE teacher_id = '$id'";
        return mysqli_query($this->conn, $query);
    }

    // Delete teacher from database
    public function deleteTeacher($id) {
        $query = "DELETE FROM Teacher 
                  WHERE teacher_id = '$id'";
        return mysqli_query($this->conn, $query);
    }

    // Count total active teachers
    public function countActiveTeachers() {
        $query = "SELECT COUNT(*) as total 
                  FROM Teacher WHERE is_active = 1";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    // Count all teachers
    public function countAllTeachers() {
        $query = "SELECT COUNT(*) as total FROM Teacher";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    // Get total enrolled students for a teacher
    public function getEnrolledStudents($teacher_id) {
        $query = "SELECT COUNT(*) as total 
                  FROM Student 
                  WHERE teacher_id = '$teacher_id'";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
}
?>