<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Grade</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 40px auto; padding: 0 20px; }
        h2 { margin-bottom: 20px; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        .error { color: red; font-size: 13px; margin-top: 4px; display: none; }
        button { margin-top: 20px; padding: 10px 24px; background: #4a90d9; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:disabled { background: #aaa; cursor: not-allowed; }
        .msg { margin-top: 15px; padding: 10px; border-radius: 4px; display: none; }
        .msg.success { background: #e6f4ea; color: #2d6a2d; }
        .msg.error-msg { background: #fdecea; color: #a61c00; }
    </style>
</head>
<body>
    <h2>Add Grade</h2>
    <label>Student ID</label>
    <input type="number" id="student_id" placeholder="Enter student ID">
    <div class="error" id="err_student">Student ID is required.</div>

    <label>Subject Name</label>
    <input type="text" id="subject_name" placeholder="e.g. Mathematics">
    <div class="error" id="err_subject">Subject name is required.</div>

    <label>Grade Value (0–100)</label>
    <input type="number" id="grade_value" min="0" max="100" step="0.01" placeholder="e.g. 87.50">
    <div class="error" id="err_grade">Grade must be between 0 and 100.</div>

    <label>Grade Date</label>
    <input type="date" id="grade_date">
    <div class="error" id="err_date">Date is required.</div>

    <button id="submitBtn" disabled onclick="submitGrade()">Add Grade</button>
    <div class="msg" id="msg"></div>

    <script>
        const fields = ['student_id','subject_name','grade_value','grade_date'];
        fields.forEach(id => {
            document.getElementById(id).addEventListener('input', validate);
        });

        function validate() {
            let valid = true;
            const sid = document.getElementById('student_id').value;
            const sub = document.getElementById('subject_name').value.trim();
            const gv  = document.getElementById('grade_value').value;
            const gd  = document.getElementById('grade_date').value;

            show('err_student', !sid);
            show('err_subject', !sub);
            show('err_grade', gv === '' || gv < 0 || gv > 100);
            show('err_date', !gd);

            if (!sid || !sub || gv === '' || gv < 0 || gv > 100 || !gd) valid = false;
            document.getElementById('submitBtn').disabled = !valid;
        }

        function show(id, condition) {
            document.getElementById(id).style.display = condition ? 'block' : 'none';
        }

        function submitGrade() {
            const data = new FormData();
            data.append('student_id',   document.getElementById('student_id').value);
            data.append('subject_name', document.getElementById('subject_name').value);
            data.append('grade_value',  document.getElementById('grade_value').value);
            data.append('grade_date',   document.getElementById('grade_date').value);

            fetch('add.php', { method: 'POST', body: data })
            .then(r => r.json())
            .then(res => {
                const msg = document.getElementById('msg');
                msg.style.display = 'block';
                if (res.success) {
                    msg.className = 'msg success';
                    msg.textContent = res.success;
                } else {
                    msg.className = 'msg error-msg';
                    msg.textContent = res.error;
                }
            });
        }
    </script>
</body>
</html>
=======
<?php
// add_grade.php - Add a new grade record
require_once '../../db.php';
header('Content-Type: application/json');

$student_id = $_POST['student_id'] ?? '';
$course_id  = $_POST['course_id']  ?? '';
$mid_term   = $_POST['mid_term']   ?? '';
$final_term = $_POST['final_term'] ?? '';

if (!$student_id || !$course_id || $mid_term === '' || $final_term === '') {
    echo json_encode(['error' => 'All fields are required.']);
    exit;
}

if (!is_numeric($mid_term) || $mid_term < 0 || $mid_term > 100) {
    echo json_encode(['error' => 'Mid-term must be between 0 and 100.']);
    exit;
}

if (!is_numeric($final_term) || $final_term < 0 || $final_term > 100) {
    echo json_encode(['error' => 'Final term must be between 0 and 100.']);
    exit;
}

$total_grade = $mid_term + $final_term;
$percentage  = ($total_grade / 200) * 100;
$is_passed   = $percentage >= 50 ? 1 : 0;

$stmt = $conn->prepare("INSERT INTO grade (student_id, course_id, mid_term, final_term, total_grade, percentage, is_passed) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('isddddi', $student_id, $course_id, $mid_term, $final_term, $total_grade, $percentage, $is_passed);
if ($stmt->execute()) {
    echo json_encode(['success' => 'Grade added successfully.']);
} else {
    echo json_encode(['error' => 'Failed to add grade.']);
}
?>
>>>>>>> ba5326336744b8692eb3be15b07a316f35b9e035
