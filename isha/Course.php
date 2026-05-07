<?php
// Course.php - Course Class (Middle Layer)
// Developer: Isha | Module: User Authentication
// Project: Edu Team - Student Record System

class Course {
    // Database connection variable
    private $conn;

    // Constructor - receives database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all courses with optional search and filter
    public function getAllCourses($search = '', $filter = '') {
        // Base query
        $query = "SELECT * FROM Course WHERE 1=1";

        // Add search by name or course code
        if($search != '') {
            $query .= " AND (course_name LIKE '%$search%' 
                        OR course_code LIKE '%$search%')";
        }

        // Add filter by active status
        if($filter == 'active') {
            $query .= " AND is_active = 1";
        } elseif($filter == 'inactive') {
            $query .= " AND is_active = 0";
        }

        return mysqli_query($this->conn, $query);
    }

    // Get single course by ID
    public function getCourse($id) {
        $query = "SELECT * FROM Course 
                  WHERE course_id = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Add new course to database
    public function addCourse($data) {
        $query = "INSERT INTO Course 
        (course_name, course_code, teacher_id, 
        is_active, start_date) 
        VALUES (
        '{$data['course_name']}',
        '{$data['course_code']}',
        '{$data['teacher_id']}',
        '{$data['is_active']}',
        '{$data['start_date']}')";
        return mysqli_query($this->conn, $query);
    }

    // Update existing course in database
    public function updateCourse($id, $data) {
        $query = "UPDATE Course SET 
        course_name = '{$data['course_name']}',
        course_code = '{$data['course_code']}',
        teacher_id = '{$data['teacher_id']}',
        is_active = '{$data['is_active']}',
        start_date = '{$data['start_date']}'
        WHERE course_id = '$id'";
        return mysqli_query($this->conn, $query);
    }

    // Delete course from database
    public function deleteCourse($id) {
        $query = "DELETE FROM Course 
                  WHERE course_id = '$id'";
        return mysqli_query($this->conn, $query);
    }

    // Count all courses
    public function countAllCourses() {
        $query = "SELECT COUNT(*) as total FROM Course";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
}
?>