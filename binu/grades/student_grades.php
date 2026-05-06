<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student - My Grades</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; min-height: 100vh; }

        .header { background: #0f6e56; color: white; padding: 16px 30px; display: flex; align-items: center; justify-content: space-between; }
        .header h1 { font-size: 20px; font-weight: 600; }
        .header span { font-size: 13px; opacity: 0.8; }

        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }

        .card { background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); margin-bottom: 24px; overflow: hidden; }
        .card-head { background: #0f6e56; color: white; padding: 14px 20px; font-size: 15px; font-weight: 600; }
        .card-body { padding: 20px; }

        .search-row { display: flex; gap: 12px; align-items: flex-end; }
        .form-group { display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .form-group label { font-size: 13px; font-weight: 600; color: #444; }
        .form-group input { padding: 9px 12px; border: 1.5px solid #ddd; border-radius: 6px; font-size: 14px; transition: border 0.2s; }
        .form-group input:focus { outline: none; border-color: #0f6e56; }
        .btn-search { padding: 9px 24px; background: #0f6e56; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; white-space: nowrap; }
        .btn-search:hover { background: #085041; }

        .err-msg { font-size: 12px; color: #e74c3c; margin-top: 6px; display: none; }

        /* Summary boxes */
        .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; display: none; }
        .summary-box { background: #f8f9fa; border: 1.5px solid #e0e0e0; border-radius: 8px; padding: 14px 16px; text-align: center; }
        .summary-box label { font-size: 11px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: 0.04em; display: block; margin-bottom: 6px; }
        .summary-box span { font-size: 22px; font-weight: 700; }
        .s-blue { color: #1a3c6e; }
        .s-green { color: #0f6e56; }
        .s-orange { color: #856404; }
        .s-red { color: #a61c00; }

        table { width: 100%; border-collapse: collapse; display: none; }
        th { background: #f0f7f4; color: #0f6e56; font-size: 12px; font-weight: 700; padding: 11px 14px; text-align: left; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 2px solid #b2d8cc; }
        td { padding: 12px 14px; font-size: 14px; color: #333; border-bottom: 1px solid #f0f0f0; }
        tr:hover td { background: #f0f7f4; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .pass { background: #e6f4ea; color: #2d6a2d; }
        .fail { background: #fdecea; color: #a61c00; }
        .pct { font-weight: 700; }
        .pct.high { color: #2d6a2d; }
        .pct.mid { color: #856404; }
        .pct.low { color: #a61c00; }

        .empty-state { text-align: center; padding: 50px 20px; color: #999; }
        .empty-state .icon { font-size: 40px; margin-bottom: 12px; }
        .empty-state p { font-size: 14px; }

        .table-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid #eee; }
        .table-head h3 { font-size: 15px; font-weight: 600; color: #0f6e56; }
        .record-count { font-size: 12px; color: #888; }
    </style>
</head>
<body>

<div class="header">
    <h1>📋 My Grades</h1>
    <span>Student View · EduTeam</span>
</div>

<div class="container">

    <!-- Search by Student ID -->
    <div class="card">
        <div class="card-head">View My Grades</div>
        <div class="card-body">
            <div class="search-row">
                <div class="form-group">
                    <label>Enter Your Student ID</label>
                    <input type="number" id="student_id" placeholder="e.g. 101" onkeydown="if(event.key==='Enter') searchGrades()">
                </div>
                <button class="btn-search" onclick="searchGrades()">View My Grades</button>
            </div>
            <div class="err-msg" id="err">Please enter a valid Student ID.</div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary" id="summary">
        <div class="summary-box">
            <label>Courses</label>
            <span class="s-blue" id="s_courses">0</span>
        </div>
        <div class="summary-box">
            <label>Avg Percentage</label>
            <span class="s-green" id="s_avg">0%</span>
        </div>
        <div class="summary-box">
            <label>Passed</label>
            <span class="s-green" id="s_passed">0</span>
        </div>
        <div class="summary-box">
            <label>Failed</label>
            <span class="s-red" id="s_failed">0</span>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card" id="resultsCard" style="display:none;">
        <div class="table-head">
            <h3 id="tableTitle">Grade Results</h3>
            <span class="record-count" id="recordCount"></span>
        </div>
        <table id="gradesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Course ID</th>
                    <th>Mid-Term</th>
                    <th>Final Term</th>
                    <th>Total</th>
                    <th>Percentage</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>
        <div class="empty-state" id="emptyState" style="display:none;">
            <div class="icon">📭</div>
            <p>No grades found for this Student ID.</p>
        </div>
    </div>

</div>

<script>
    function searchGrades() {
        const sid = document.getElementById('student_id').value.trim();
        const err = document.getElementById('err');

        if (!sid || isNaN(sid)) {
            err.style.display = 'block';
            return;
        }
        err.style.display = 'none';

        fetch('grade_list.php?student_id=' + sid)
            .then(r => r.json())
            .then(data => {
                const summary    = document.getElementById('summary');
                const resultsCard = document.getElementById('resultsCard');
                const gradesTable = document.getElementById('gradesTable');
                const emptyState = document.getElementById('emptyState');
                const tbody      = document.getElementById('tableBody');

                summary.style.display     = 'grid';
                resultsCard.style.display = 'block';

                document.getElementById('tableTitle').textContent = 'Grades for Student ID: ' + sid;
                document.getElementById('recordCount').textContent = data.length + ' record' + (data.length !== 1 ? 's' : '');

                if (data.length === 0) {
                    gradesTable.style.display = 'none';
                    emptyState.style.display  = 'block';
                    document.getElementById('s_courses').textContent = '0';
                    document.getElementById('s_avg').textContent     = '0%';
                    document.getElementById('s_passed').textContent  = '0';
                    document.getElementById('s_failed').textContent  = '0';
                    return;
                }

                gradesTable.style.display = 'table';
                emptyState.style.display  = 'none';

                // Summary calculations
                const total   = data.length;
                const passed  = data.filter(g => g.is_passed == 1).length;
                const failed  = total - passed;
                const avgPct  = data.reduce((sum, g) => sum + parseFloat(g.percentage), 0) / total;

                document.getElementById('s_courses').textContent = total;
                document.getElementById('s_avg').textContent     = avgPct.toFixed(2) + '%';
                document.getElementById('s_passed').textContent  = passed;
                document.getElementById('s_failed').textContent  = failed;

                tbody.innerHTML = data.map((g, i) => {
                    const pct = parseFloat(g.percentage);
                    const pctClass = pct >= 70 ? 'high' : pct >= 50 ? 'mid' : 'low';
                    return `<tr>
                        <td>${i + 1}</td>
                        <td>${g.course_id}</td>
                        <td>${parseFloat(g.mid_term).toFixed(2)}</td>
                        <td>${parseFloat(g.final_term).toFixed(2)}</td>
                        <td>${parseFloat(g.total_grade).toFixed(2)}</td>
                        <td><span class="pct ${pctClass}">${pct.toFixed(2)}%</span></td>
                        <td><span class="badge ${g.is_passed == 1 ? 'pass' : 'fail'}">${g.is_passed == 1 ? '✓ Passed' : '✗ Failed'}</span></td>
                    </tr>`;
                }).join('');
            });
    }
</script>
</body>
</html>