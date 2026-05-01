<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Grades</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px; }
        h2 { margin-bottom: 20px; color: #333; }
        .toolbar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .toolbar input, .toolbar select { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .toolbar input[type="text"] { width: 220px; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #4a90d9; color: white; }
        .btn-primary:hover { background: #357abd; }
        table { width: 100%; background: white; border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        th { background: #4a90d9; color: white; padding: 12px 16px; text-align: left; font-size: 14px; }
        td { padding: 12px 16px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f9f9f9; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .pass { background: #e6f4ea; color: #2d6a2d; }
        .fail { background: #fdecea; color: #a61c00; }
        .letter { display: inline-block; width: 28px; height: 28px; border-radius: 50%; background: #4a90d9; color: white; text-align: center; line-height: 28px; font-weight: bold; font-size: 13px; }
        .btn-edit { background: #f0ad4e; color: white; padding: 5px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; margin-right: 4px; }
        .btn-edit:hover { background: #d9922a; }
        .btn-del { background: #e74c3c; color: white; padding: 5px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .btn-del:hover { background: #c0392b; }
        .empty { text-align: center; padding: 40px; color: #999; }
        .pagination { margin-top: 16px; display: flex; gap: 8px; justify-content: center; }
        .pagination button { padding: 6px 14px; border: 1px solid #ccc; border-radius: 4px; background: white; cursor: pointer; font-size: 13px; }
        .pagination button.active { background: #4a90d9; color: white; border-color: #4a90d9; }
        .pagination button:hover:not(.active) { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Grade Records</h2>

    <div class="toolbar">
        <input type="text" id="searchInput" placeholder="Search by subject..." oninput="applyFilters()">
        <select id="filterPassed" onchange="applyFilters()">
            <option value="">All results</option>
            <option value="1">Passed only</option>
            <option value="0">Failed only</option>
        </select>
        <select id="filterLetter" onchange="applyFilters()">
            <option value="">All grades</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="F">F</option>
        </select>
        <a href="add_grade.php"><button class="btn btn-primary">+ Add Grade</button></a>
    </div>

    <table id="gradesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Subject</th>
                <th>Grade</th>
                <th>Letter</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <tr><td colspan="8" class="empty">Loading...</td></tr>
        </tbody>
    </table>

    <div class="pagination" id="pagination"></div>

    <script>
        let allGrades = [];
        let filtered = [];
        const perPage = 10;
        let currentPage = 1;

        fetch('list.php')
            .then(r => r.json())
            .then(data => {
                allGrades = data;
                filtered = data;
                render();
            });

        function applyFilters() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const passed = document.getElementById('filterPassed').value;
            const letter = document.getElementById('filterLetter').value;

            filtered = allGrades.filter(g => {
                const matchSearch = g.subject_name.toLowerCase().includes(search) ||
                                    g.student_id.toString().includes(search);
                const matchPassed = passed === '' || g.is_passed == passed;
                const matchLetter = letter === '' || g.grade_letter === letter;
                return matchSearch && matchPassed && matchLetter;
            });
            currentPage = 1;
            render();
        }

        function render() {
            const tbody = document.getElementById('tableBody');
            const start = (currentPage - 1) * perPage;
            const page = filtered.slice(start, start + perPage);

            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="empty">No grades found.</td></tr>';
                document.getElementById('pagination').innerHTML = '';
                return;
            }

            tbody.innerHTML = page.map((g, i) => `
                <tr>
                    <td>${start + i + 1}</td>
                    <td>${g.student_id}</td>
                    <td>${g.subject_name}</td>
                    <td>${parseFloat(g.grade_value).toFixed(2)}</td>
                    <td><span class="letter">${g.grade_letter}</span></td>
                    <td><span class="badge ${g.is_passed == 1 ? 'pass' : 'fail'}">${g.is_passed == 1 ? 'Passed' : 'Failed'}</span></td>
                    <td>${g.grade_date}</td>
                    <td>
                        <button class="btn-edit" onclick="location.href='edit_grade.php?id=${g.grade_id}'">Edit</button>
                        <button class="btn-del" onclick="deleteGrade(${g.grade_id})">Delete</button>
                    </td>
                </tr>
            `).join('');

            renderPagination();
        }

        function renderPagination() {
            const total = Math.ceil(filtered.length / perPage);
            const pg = document.getElementById('pagination');
            if (total <= 1) { pg.innerHTML = ''; return; }
            pg.innerHTML = Array.from({length: total}, (_, i) =>
                `<button class="${i+1 === currentPage ? 'active' : ''}" onclick="goPage(${i+1})">${i+1}</button>`
            ).join('');
        }

        function goPage(n) { currentPage = n; render(); }

        function deleteGrade(id) {
            if (!confirm('Are you sure you want to delete this grade? This cannot be undone.')) return;
            fetch('delete.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'grade_id=' + id
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    allGrades = allGrades.filter(g => g.grade_id != id);
                    applyFilters();
                } else {
                    alert('Error: ' + res.error);
                }
            });
        }
    </script>
</body>
</html>