<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher - Grade Management</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
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

        /* ── CHARTS SECTION ── */
        .charts-section { margin-bottom: 24px; }
        .charts-section h2 { font-size: 16px; font-weight: 700; color: #1a3c6e; margin-bottom: 14px; }

        .stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .stat-box { background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 18px 20px; border-top: 4px solid #1a3c6e; }
        .stat-box.pass-color { border-top-color: #2d6a2d; }
        .stat-box.fail-color { border-top-color: #a61c00; }
        .stat-box.rate-color { border-top-color: #856404; }
        .stat-box .s-label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
        .stat-box .s-value { font-size: 30px; font-weight: 700; color: #1a3c6e; }
        .stat-box.pass-color .s-value { color: #2d6a2d; }
        .stat-box.fail-color .s-value { color: #a61c00; }
        .stat-box.rate-color .s-value { color: #856404; }

        .charts-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .chart-box { background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 20px; }
        .chart-box h3 { font-size: 14px; font-weight: 700; color: #1a3c6e; margin-bottom: 4px; }
        .chart-box p { font-size: 12px; color: #888; margin-bottom: 14px; }
        .chart-wrap { position: relative; height: 200px; display: flex; align-items: center; justify-content: center; }
        .chart-wrap-bar { position: relative; height: 200px; }

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
        .pct.mid  { color: #856404; }
        .pct.low  { color: #a61c00; }

        .btn-del { background: #fdecea; color: #a61c00; border: none; padding: 5px 12px; border-radius: 4px; font-size: 12px; cursor: pointer; font-weight: 600; }
        .btn-del:hover { background: #e74c3c; color: white; }
        .btn-edit { background: #e8f0fe; color: #1a3c6e; border: none; padding: 5px 12px; border-radius: 4px; font-size: 12px; cursor: pointer; font-weight: 600; margin-right: 4px; }
        .btn-edit:hover { background: #1a3c6e; color: white; }

        /* Search bar */
        .search-row { display: flex; gap: 10px; align-items: center; padding: 14px 20px; border-bottom: 1px solid #eee; }
        .search-row input { padding: 8px 12px; border: 1.5px solid #ddd; border-radius: 6px; font-size: 14px; width: 200px; }
        .search-row input:focus { outline: none; border-color: #1a3c6e; }
        .search-row select { padding: 8px 12px; border: 1.5px solid #ddd; border-radius: 6px; font-size: 14px; }
        .search-row button { padding: 8px 16px; background: #1a3c6e; color: white; border: none; border-radius: 6px; font-size: 13px; cursor: pointer; }
        .search-row .btn-clear { background: #eee; color: #444; }

        /* Edit Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 12px; padding: 28px; width: 420px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .modal h3 { font-size: 16px; font-weight: 700; color: #1a3c6e; margin-bottom: 20px; }
        .modal .form-group { margin-bottom: 14px; }
        .modal label { font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 5px; }
        .modal input { width: 100%; padding: 9px 12px; border: 1.5px solid #ddd; border-radius: 6px; font-size: 14px; }
        .modal input:focus { outline: none; border-color: #1a3c6e; }
        .modal input[readonly] { background: #f8f9fa; color: #888; }
        .modal-btns { display: flex; gap: 10px; margin-top: 20px; }
        .modal-btns button { flex: 1; padding: 10px; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .btn-save { background: #1a3c6e; color: white; }
        .btn-save:hover { background: #14305a; }
        .btn-cancel { background: #eee; color: #444; }
        .modal-msg { font-size: 13px; margin-top: 10px; padding: 8px 12px; border-radius: 6px; display: none; }
        .modal-msg.success { background: #e6f4ea; color: #2d6a2d; }
        .modal-msg.error { background: #fdecea; color: #a61c00; }
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

    <!-- ── CHARTS SECTION ── -->
    <div class="charts-section">
        <h2>📈 Pass / Fail Analysis</h2>

        <!-- Stat Cards -->
        <div class="stat-row">
            <div class="stat-box">
                <div class="s-label">Total Records</div>
                <div class="s-value" id="c-total">—</div>
            </div>
            <div class="stat-box pass-color">
                <div class="s-label">Total Passed</div>
                <div class="s-value" id="c-pass">—</div>
            </div>
            <div class="stat-box fail-color">
                <div class="s-label">Total Failed</div>
                <div class="s-value" id="c-fail">—</div>
            </div>
            <div class="stat-box rate-color">
                <div class="s-label">Pass Rate</div>
                <div class="s-value" id="c-rate">—</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-box">
                <h3>Mid-Term</h3>
                <p>Pass vs Fail distribution</p>
                <div class="chart-wrap">
                    <canvas id="midChart"></canvas>
                </div>
            </div>
            <div class="chart-box">
                <h3>Final Term</h3>
                <p>Pass vs Fail distribution</p>
                <div class="chart-wrap">
                    <canvas id="finalChart"></canvas>
                </div>
            </div>
            <div class="chart-box">
                <h3>Mid vs Final</h3>
                <p>Pass count comparison</p>
                <div class="chart-wrap-bar">
                    <canvas id="compareChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editModal">
        <div class="modal">
            <h3>✏️ Edit Grade Record</h3>
            <input type="hidden" id="edit_grade_id">
            <div class="form-group">
                <label>Student ID</label>
                <input type="text" id="edit_student_id" readonly>
            </div>
            <div class="form-group">
                <label>Course ID</label>
                <input type="text" id="edit_course_id" readonly>
            </div>
            <div class="form-group">
                <label>Mid-Term Score (0–100)</label>
                <input type="number" id="edit_mid" min="0" max="100" step="0.01" oninput="calcEdit()">
            </div>
            <div class="form-group">
                <label>Final Term Score (0–100)</label>
                <input type="number" id="edit_final" min="0" max="100" step="0.01" oninput="calcEdit()">
            </div>
            <div class="calc-row" style="margin-top:14px">
                <div class="calc-box">
                    <label>Total</label>
                    <span id="edit_total">—</span>
                </div>
                <div class="calc-box">
                    <label>Percentage</label>
                    <span id="edit_pct">—</span>
                </div>
                <div class="calc-box">
                    <label>Result</label>
                    <span id="edit_result">—</span>
                </div>
            </div>
            <div class="modal-msg" id="editMsg"></div>
            <div class="modal-btns">
                <button class="btn-save" onclick="saveEdit()">Save Changes</button>
                <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="card">
        <div class="table-head">
            <h3>All Grade Records</h3>
            <span class="record-count" id="recordCount">Loading...</span>
        </div>

        <!-- Search Bar -->
        <div class="search-row">
            <input type="text" id="searchInput" placeholder="Search Student ID or Course..." oninput="filterTable()">
            <select id="searchFilter" onchange="filterTable()">
                <option value="all">All Results</option>
                <option value="pass">Passed Only</option>
                <option value="fail">Failed Only</option>
            </select>
            <button class="btn-clear" onclick="clearSearch()">Clear</button>
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
    // pass mark: 50% of total (same as your existing logic)
    const PASS_PCT = 50;

    let midChart, finalChart, compareChart;
    let allData = [];

    // Load grades on page load
    loadGrades();

    function loadGrades() {
        fetch('list.php')
            .then(r => r.json())
            .then(data => {
                allData = data;
                renderTable(data);
                renderCharts(data);
            });
    }

    function renderTable(data) {
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
            return `<tr data-sid="${g.student_id}" data-cid="${g.course_id}" data-passed="${g.is_passed}">
                <td>${i + 1}</td>
                <td>${g.student_id}</td>
                <td>${g.course_id}</td>
                <td>${parseFloat(g.mid_term).toFixed(2)}</td>
                <td>${parseFloat(g.final_term).toFixed(2)}</td>
                <td>${parseFloat(g.total_grade).toFixed(2)}</td>
                <td><span class="pct ${pctClass}">${pct.toFixed(2)}%</span></td>
                <td><span class="badge ${g.is_passed == 1 ? 'pass' : 'fail'}">${g.is_passed == 1 ? 'Passed' : 'Failed'}</span></td>
                <td>
                    <button class="btn-edit" onclick="openEdit(${g.grade_id},'${g.student_id}','${g.course_id}',${g.mid_term},${g.final_term})">Edit</button>
                    <button class="btn-del" onclick="deleteGrade(${g.grade_id})">Delete</button>
                </td>
            </tr>`;
        }).join('');
    }

    // ── SEARCH ──────────────────────────────────────
    function filterTable() {
        const q      = document.getElementById('searchInput').value.toLowerCase();
        const filter = document.getElementById('searchFilter').value;

        const filtered = allData.filter(g => {
            const matchText = g.student_id.toString().includes(q) || g.course_id.toLowerCase().includes(q);
            const matchFilter = filter === 'all' ? true : filter === 'pass' ? g.is_passed == 1 : g.is_passed == 0;
            return matchText && matchFilter;
        });
        renderTable(filtered);
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('searchFilter').value = 'all';
        renderTable(allData);
    }

    // ── EDIT ──────────────────────────────────────
    function openEdit(id, sid, cid, mid, final) {
        document.getElementById('edit_grade_id').value  = id;
        document.getElementById('edit_student_id').value = sid;
        document.getElementById('edit_course_id').value  = cid;
        document.getElementById('edit_mid').value   = mid;
        document.getElementById('edit_final').value = final;
        document.getElementById('editMsg').style.display = 'none';
        calcEdit();
        document.getElementById('editModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('editModal').classList.remove('active');
    }

    function calcEdit() {
        const mid   = parseFloat(document.getElementById('edit_mid').value);
        const final = parseFloat(document.getElementById('edit_final').value);
        if (!isNaN(mid) && !isNaN(final)) {
            const total = mid + final;
            const pct   = (total / 200) * 100;
            document.getElementById('edit_total').textContent  = total.toFixed(2);
            document.getElementById('edit_pct').textContent    = pct.toFixed(2) + '%';
            document.getElementById('edit_result').textContent = pct >= 50 ? '✅ Pass' : '❌ Fail';
        }
    }

    function saveEdit() {
        const data = new FormData();
        data.append('grade_id',   document.getElementById('edit_grade_id').value);
        data.append('mid_term',   document.getElementById('edit_mid').value);
        data.append('final_term', document.getElementById('edit_final').value);

        fetch('edit.php', { method: 'POST', body: data })
            .then(r => r.json())
            .then(res => {
                const msg = document.getElementById('editMsg');
                msg.style.display = 'block';
                if (res.success) {
                    msg.className = 'modal-msg success';
                    msg.textContent = '✓ ' + res.success;
                    setTimeout(() => { closeModal(); loadGrades(); }, 1000);
                } else {
                    msg.className = 'modal-msg error';
                    msg.textContent = '✗ ' + res.error;
                }
            });
    }

    function renderCharts(data) {
        const total      = data.length;
        const passed     = data.filter(g => g.is_passed == 1).length;
        const failed     = total - passed;
        const passRate   = total ? Math.round((passed / total) * 100) : 0;

        // Mid term pass/fail (50% of mid = 50 marks)
        const midPass  = data.filter(g => parseFloat(g.mid_term)   >= 50).length;
        const midFail  = total - midPass;

        // Final term pass/fail (50% of final = 50 marks)
        const finalPass = data.filter(g => parseFloat(g.final_term) >= 50).length;
        const finalFail = total - finalPass;

        // Stat cards
        document.getElementById('c-total').textContent = total;
        document.getElementById('c-pass').textContent  = passed;
        document.getElementById('c-fail').textContent  = failed;
        document.getElementById('c-rate').textContent  = passRate + '%';

        const pieColors = ['#2d6a2d', '#a61c00'];
        const pieOpts = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 12 } } }
            }
        };

        // Destroy old charts if exist
        if (midChart)     midChart.destroy();
        if (finalChart)   finalChart.destroy();
        if (compareChart) compareChart.destroy();

        // Mid Term Doughnut
        midChart = new Chart(document.getElementById('midChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pass', 'Fail'],
                datasets: [{ data: [midPass, midFail], backgroundColor: pieColors, borderWidth: 2 }]
            },
            options: pieOpts
        });

        // Final Term Doughnut
        finalChart = new Chart(document.getElementById('finalChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pass', 'Fail'],
                datasets: [{ data: [finalPass, finalFail], backgroundColor: pieColors, borderWidth: 2 }]
            },
            options: pieOpts
        });

        // Compare Bar
        compareChart = new Chart(document.getElementById('compareChart'), {
            type: 'bar',
            data: {
                labels: ['Mid Term', 'Final Term'],
                datasets: [
                    {
                        label: 'Pass',
                        data: [midPass, finalPass],
                        backgroundColor: 'rgba(45,106,45,0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'Fail',
                        data: [midFail, finalFail],
                        backgroundColor: 'rgba(166,28,0,0.8)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 12 } } }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                }
            }
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
                    loadGrades(); // also refreshes charts!
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
            if (res.success) loadGrades(); // also refreshes charts!
            else alert('Error: ' + res.error);
        });
    }
</script>
</body>
</html>
