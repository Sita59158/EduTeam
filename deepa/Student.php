<?php
// ============================================
// Student.php - Student Class (Middle Layer)
// Developer: Deepa Thapa
// Module: Student Profile Management
// Project: Edu Team - Student Record System
// ============================================

class Student {
    // Database connection variable
    private $conn;

    // Constructor - receives database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all students with optional search and filter
    public function getAllStudents($search = '', $filter = '') {
        // Base query - WHERE 1=1 allows easy AND conditions
        $query = "SELECT * FROM Student WHERE 1=1";

        // Add search condition if provided
        if($search != '') {
            $query .= " AND full_name LIKE '%$search%'";
        }

        // Add filter condition if provided
        if($filter == 'active') {
            $query .= " AND is_active = 1";
        } elseif($filter == 'inactive') {
            $query .= " AND is_active = 0";
        }

        return mysqli_query($this->conn, $query);
    }

    // Get single student by ID
    public function getStudent($id) {
        $query = "SELECT * FROM Student 
                  WHERE student_id = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Add new student to database
    public function addStudent($data) {
        $query = "INSERT INTO Student 
        (full_name, email, teacher_id, course_id, 
        enrolled_date, is_active) 
        VALUES (
        '{$data['full_name']}', 
        '{$data['email']}', 
        '{$data['teacher_id']}', 
        '{$data['course_id']}', 
        '{$data['enrolled_date']}', 
        '{$data['is_active']}')";
        return mysqli_query($this->conn, $query);
    }

    // Update existing student in database
    public function updateStudent($id, $data) {
        $query = "UPDATE Student SET 
        full_name = '{$data['full_name']}',
        email = '{$data['email']}',
        teacher_id = '{$data['teacher_id']}',
        course_id = '{$data['course_id']}',
        enrolled_date = '{$data['enrolled_date']}',
        is_active = '{$data['is_active']}'
        WHERE student_id = '$id'";
        return mysqli_query($this->conn, $query);
    }

    // Delete student from database
    public function deleteStudent($id) {
        $query = "DELETE FROM Student 
                  WHERE student_id = '$id'";
        return mysqli_query($this->conn, $query);
    }

    // Count total active students
    public function countActiveStudents() {
        $query = "SELECT COUNT(*) as total 
                  FROM Student WHERE is_active = 1";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    // Count total students
    public function countAllStudents() {
        $query = "SELECT COUNT(*) as total FROM Student";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
}
?>
