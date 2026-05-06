<?php require_once '../db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance List</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 20px; }
        h2 { text-align: center; color: #333; }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        input[type="text"] {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-add {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td { padding: 10px 12px; border-bottom: 1px solid #eee; }
        tr:hover { background-color: #f9f9f9; }
        .btn-edit {
            padding: 5px 10px;
            background-color: #2196F3;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn-delete {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }
        .status-present { color: green; font-weight: bold; }
        .status-absent { color: red; font-weight: bold; }
        .status-late { color: orange; font-weight: bold; }
        .warning-yes { color: red; font-weight: bold; }
        .warning-no { color: green; }
        .no-records { text-align: center; padding: 20px; color: #999; }
    </style>
</head>
<body>
    <h2>Attendance List</h2>
    <div class="top-bar">
        <div style="display:flex; gap:10px;">
            <input type="text" id="searchInput" 
                   placeholder="Search by Student ID..." 
                   onkeyup="filterTable()">
            <input type="date" id="dateFilter" 
                   onchange="filterTable()">
            <select id="statusFilter" onchange="filterTable()">
                <option value="">-- All Status --</option>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
            </select>
        </div>
        <a href="add_attendance.php" class="btn-add">+ Add Attendance</a>
    </div>

    <table id="attendanceTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Teacher ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Absence %</th>
                <th>Warning</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM attendance ORDER BY attendance_date DESC";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                $statusClass = 'status-' . strtolower($row['status']);
                $warningText = $row['warning'] ? '⚠️ Yes' : 'No';
                $warningClass = $row['warning'] ? 'warning-yes' : 'warning-no';
                echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['student_id']}</td>
                    <td>{$row['teacher_id']}</td>
                    <td>{$row['attendance_date']}</td>
                    <td class='{$statusClass}'>{$row['status']}</td>
                    <td>{$row['absence_percentage']}%</td>
                    <td class='{$warningClass}'>{$warningText}</td>
                    <td>
                        <a href='edit_attendance.php?id={$row['attendance_id']}' 
                           class='btn-edit'>Edit</a>
                        <a href='attendance.php?delete={$row['attendance_id']}' 
                           class='btn-delete'
                           onclick='return confirm(\"Are you sure you want to delete?\")'>Delete</a>
                    </td>
                </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='8' class='no-records'>No attendance records found.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <script>
        function filterTable() {
            let search = document.getElementById('searchInput').value.toLowerCase();
            let date = document.getElementById('dateFilter').value;
            let status = document.getElementById('statusFilter').value.toLowerCase();
            let rows = document.querySelectorAll('#attendanceTable tbody tr');

            rows.forEach(row => {
                let studentId = row.cells[1]?.textContent.toLowerCase() || '';
                let rowDate = row.cells[3]?.textContent.toLowerCase() || '';
                let rowStatus = row.cells[4]?.textContent.toLowerCase() || '';

                let matchSearch = studentId.includes(search);
                let matchDate = date === '' || rowDate.includes(date);
                let matchStatus = status === '' || rowStatus.includes(status);

                row.style.display = matchSearch && matchDate && matchStatus ? '' : 'none';
            });
        }
    </script>
</body>
</html>