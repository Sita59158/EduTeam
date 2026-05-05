<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 { text-align: center; color: #333; }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background-color: #45a049; }
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #4CAF50;
        }
        .error { color: red; font-size: 13px; margin: 0; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add Attendance</h2>
        <form action="attendance.php" method="POST" onsubmit="return validateForm()">
            <input type="number" name="student_id" id="student_id" 
                   placeholder="Student ID" required>
            <p class="error" id="err_student"></p>

            <input type="number" name="teacher_id" id="teacher_id" 
                   placeholder="Teacher ID" required>
            <p class="error" id="err_teacher"></p>

            <input type="date" name="attendance_date" id="attendance_date" required>
            <p class="error" id="err_date"></p>

            <select name="status" id="status" required>
                <option value="">-- Select Status --</option>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
            </select>
            <p class="error" id="err_status"></p>

            <button type="submit" name="add">Add Attendance</button>
        </form>
        <a href="attendance_list.php">← Back to List</a>
    </div>

    <script>
        function validateForm() {
            let valid = true;

            let student_id = document.getElementById('student_id').value;
            let teacher_id = document.getElementById('teacher_id').value;
            let date = document.getElementById('attendance_date').value;
            let status = document.getElementById('status').value;

            document.getElementById('err_student').textContent = '';
            document.getElementById('err_teacher').textContent = '';
            document.getElementById('err_date').textContent = '';
            document.getElementById('err_status').textContent = '';

            if (student_id === '' || student_id <= 0) {
                document.getElementById('err_student').textContent = 'Please enter a valid Student ID.';
                valid = false;
            }
            if (teacher_id === '' || teacher_id <= 0) {
                document.getElementById('err_teacher').textContent = 'Please enter a valid Teacher ID.';
                valid = false;
            }
            if (date === '') {
                document.getElementById('err_date').textContent = 'Please select a date.';
                valid = false;
            }
            if (status === '') {
                document.getElementById('err_status').textContent = 'Please select a status.';
                valid = false;
            }
            return valid;
        }
    </script>
</body>
</html>