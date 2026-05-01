<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher - Grade Management</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; min-height: 100vh; }

        .header { background: #1a3c6e; color: white; padding: 16px 30px; display: flex; align-items: center; justify-content: space-between; }
        .header h1 { font-size: 20px; font-weight: 600; }
        .header span { font-size: 13px; opacity: 0.8; }

        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }

        /* Add Grade Card */
        .card { background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); margin-bottom: 24px; overflow: hidden; }
        .card-head { background: #1a3c6e; color: white; padding: 14px 20px; font-size: 15px; font-weight: 600; }
        .card-body { padding: 20px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group label { font-size: 13px; font-weight: 600; color: #444; }
        .form-group input { padding: 9px 12px; border: 1.5px solid #ddd; border-radius: 6px; font-size: 14px; transition: border 0.2s; }
        .form-group input:focus { outline: none; border-color: #1a3c6e; }
        .form-group input.error { border-color: #e74c3c; }
        .err-msg { font-size: 11px; color: #e74c3c; display: none; }

        .calc-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-top: 16px; }
        .calc-box { background: #f8f9fa; border: 1.5px solid #e0e0e0; border-radius: 6px; padding: 10px 14px; }
        .calc-box label { font-size: 11px; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.04em; display: block; margin-bottom: 4px; }
        .calc-box span { font-size: 18px; font-weight: 700; color: #1a3c6e; }

        .btn-row { margin-top: 20px; display: flex; gap: 10px; align-items: center; }
        .btn-submit { padding: 10px 28px; background: #1a3c6e; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-submit:hover { background: #14305a; }
        .btn-submit:disabled { background: #aaa; cursor: not-allowed; }
        .btn-reset { padding: 10px 20px; background: #eee; color: #444; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; }
        .msg { padding: 9px 16px; border-radius: 6px; font-size: 13px; display: none; }
        .msg.success { background: #e6f4ea; color: #2d6a2d; }
        .msg.error { background: #fdecea; color: #a61c00; }

        /* Table */
        .table-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid #eee; }
        .table-head h3 { font-size: 15px; font-weight: 600; color: #1a3c6e; }
        .record-count { font-size: 12px; color: #888; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f0f4fa; color: #1a3c6e; font-size: 12px; font-weight: 700; padding: 11px 14px; text-align: left; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 2px solid #d0daea; }
        td { padding: 12px 14px; font-size: 14px; color: #333; border-bottom: 1px solid #f0f0f0; }
        tr:hover td { background: #f8faff; }
        .empty-row td { text-align: center; color: #999; padding: 40px; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .pass { background: #e6f4ea; color: #2d6a2d; }
        .fail { background: #fdecea; color: #a61c00; }
        .pct { font-weight: 700; }
        .pct.high { color: #2d6a2d; }
        .pct.mid { color: #856404; }
        .pct.low { color: #a61c00; }

        .btn-del { background: #fdecea; color: #a61c00; border: none; padding: 5px 12px; border-radius: 4px; font-size: 12px; cursor: pointer; font-weight: 600; }
        .btn-del:hover { background: #e74c3c; color: white; }
    </style>
</head>
<body>

<div class="header">
    <h1>📊 Grade Management</h1>
    <span>Teacher View · EduTeam</span>
</div>

<div class="container">

    <!-- Add Grade Form -->
    <div class="card">
        <div class="card-head">Add New Grade</div>
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="number" id="student_id" placeholder="e.g. 101" oninput="validate()">
                    <span class="err-msg" id="e_student">Student ID is required.</span>
                </div>
                <div class="form-group">
                    <label>Course ID</label>
                    <input type="text" id="course_id" placeholder="e.g. MATH101" oninput="validate()">
                    <span class="err-msg" id="e_course">Course ID is required.</span>
                </div>
                <div class="form-group">
                    <label>Mid-Term Score (0–100)</label>
                    <input type="number" id="mid_term" min="0" max="100" step="0.01" placeholder="e.g. 75" oninput="validate(); calculate();">
                    <span class="err-msg" id="e_mid">Must be between 0 and 100.</span>
                </div>
                <div class="form-group">
                    <label>Final Term Score (0–100)</label>
                    <input type="number" id="final_term" min="0" max="100" step="0.01" placeholder="e.g. 80" oninput="validate(); calculate();">
                    <span class="err-msg" id="e_final">Must be between 0 and 100.</span>
                </div>
            </div>

            <div class="calc-row">
                <div class="calc-box">
                    <label>Total Grade</label>
                    <span id="show_total">—</span>
                </div>
                <div class="calc-box">
                    <label>Percentage</label>
                    <span id="show_pct">—</span>
                </div>
                <div class="calc-box">
                    <label>Result</label>
                    <span id="show_pass">—</span>
                </div>
            </div>

            <div class="btn-row">
                <button class="btn-submit" id="submitBtn" disabled onclick="submitGrade()">Add Grade</button>
                <button class="btn-reset" onclick="resetForm()">Clear</button>
                <div class="msg" id="formMsg"></div>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="card">
        <div class="table-head">
            <h3>All Grade Records</h3>
            <span class="record-count" id="recordCount">Loading...</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>Course ID</th>
                    <th>Mid-Term</th>
                    <th>Final Term</th>
                    <th>Total</th>
                    <th>Percentage</th>
                    <th>Result</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr class="empty-row"><td colspan="9">Loading grades...</td></tr>
            </tbody>
        </table>
    </div>

</div>

<script>
    // Load grades on page load
    loadGrades();

    function loadGrades() {
        fetch('list.php')
            .then(r => r.json())
            .then(data => {
                const tbody = document.getElementById('tableBody');
                const count = document.getElementById('recordCount');
                count.textContent = data.length + ' record' + (data.length !== 1 ? 's' : '');

                if (data.length === 0) {
                    tbody.innerHTML = '<tr class="empty-row"><td colspan="9">No grades recorded yet.</td></tr>';
                    return;
                }

                tbody.innerHTML = data.map((g, i) => {
                    const pct = parseFloat(g.percentage);
                    const pctClass = pct >= 70 ? 'high' : pct >= 50 ? 'mid' : 'low';
                    return `<tr>
                        <td>${i + 1}</td>
                        <td>${g.student_id}</td>
                        <td>${g.course_id}</td>
                        <td>${parseFloat(g.mid_term).toFixed(2)}</td>
                        <td>${parseFloat(g.final_term).toFixed(2)}</td>
                        <td>${parseFloat(g.total_grade).toFixed(2)}</td>
                        <td><span class="pct ${pctClass}">${pct.toFixed(2)}%</span></td>
                        <td><span class="badge ${g.is_passed == 1 ? 'pass' : 'fail'}">${g.is_passed == 1 ? 'Passed' : 'Failed'}</span></td>
                        <td><button class="btn-del" onclick="deleteGrade(${g.grade_id})">Delete</button></td>
                    </tr>`;
                }).join('');
            });
    }

    function calculate() {
        const mid   = parseFloat(document.getElementById('mid_term').value);
        const final = parseFloat(document.getElementById('final_term').value);

        if (!isNaN(mid) && !isNaN(final)) {
            const total = mid + final;
            const pct   = (total / 200) * 100;
            const pass  = pct >= 50;

            document.getElementById('show_total').textContent = total.toFixed(2);
            document.getElementById('show_pct').textContent   = pct.toFixed(2) + '%';
            document.getElementById('show_pass').textContent  = pass ? '✅ Pass' : '❌ Fail';
        } else {
            document.getElementById('show_total').textContent = '—';
            document.getElementById('show_pct').textContent   = '—';
            document.getElementById('show_pass').textContent  = '—';
        }
    }

    function validate() {
        const sid   = document.getElementById('student_id').value;
        const cid   = document.getElementById('course_id').value.trim();
        const mid   = document.getElementById('mid_term').value;
        const final = document.getElementById('final_term').value;

        show('e_student', !sid);
        show('e_course',  !cid);
        show('e_mid',   mid   === '' || mid   < 0 || mid   > 100);
        show('e_final', final === '' || final < 0 || final > 100);

        const valid = sid && cid &&
                      mid !== '' && mid >= 0 && mid <= 100 &&
                      final !== '' && final >= 0 && final <= 100;
        document.getElementById('submitBtn').disabled = !valid;
    }

    function show(id, condition) {
        document.getElementById(id).style.display = condition ? 'block' : 'none';
    }

    function submitGrade() {
        const data = new FormData();
        data.append('student_id', document.getElementById('student_id').value);
        data.append('course_id',  document.getElementById('course_id').value);
        data.append('mid_term',   document.getElementById('mid_term').value);
        data.append('final_term', document.getElementById('final_term').value);

        fetch('add.php', { method: 'POST', body: data })
            .then(r => r.json())
            .then(res => {
                const msg = document.getElementById('formMsg');
                msg.style.display = 'block';
                if (res.success) {
                    msg.className = 'msg success';
                    msg.textContent = '✓ ' + res.success;
                    resetForm();
                    loadGrades();
                } else {
                    msg.className = 'msg error';
                    msg.textContent = '✗ ' + res.error;
                }
                setTimeout(() => msg.style.display = 'none', 3000);
            });
    }

    function resetForm() {
        ['student_id','course_id','mid_term','final_term'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('show_total').textContent = '—';
        document.getElementById('show_pct').textContent   = '—';
        document.getElementById('show_pass').textContent  = '—';
        document.getElementById('submitBtn').disabled = true;
    }

    function deleteGrade(id) {
        if (!confirm('Delete this grade record? This cannot be undone.')) return;
        fetch('delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'grade_id=' + id
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) loadGrades();
            else alert('Error: ' + res.error);
        });
    }
</script>
</body>
</html>