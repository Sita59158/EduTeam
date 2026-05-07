<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student - My Grade Progress</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; min-height: 100vh; }
        .header { background: #1a3c6e; color: white; padding: 16px 30px; display: flex; align-items: center; justify-content: space-between; }
        .header h1 { font-size: 20px; font-weight: 600; }
        .header span { font-size: 13px; opacity: 0.8; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .search-card { background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .search-card label { font-size: 14px; font-weight: 600; color: #444; }
        .search-card input { padding: 9px 14px; border: 1.5px solid #ddd; border-radius: 6px; font-size: 14px; width: 160px; }
        .search-card input:focus { outline: none; border-color: #1a3c6e; }
        .search-card button { padding: 9px 22px; background: #1a3c6e; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .search-card button:hover { background: #14305a; }
        .hint { font-size: 13px; color: #888; }
        .stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .stat-box { background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 18px 20px; border-top: 4px solid #1a3c6e; }
        .stat-box.pass-c { border-top-color: #2d6a2d; }
        .stat-box.impr-c { border-top-color: #856404; }
        .stat-box .s-label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
        .stat-box .s-value { font-size: 28px; font-weight: 700; color: #1a3c6e; }
        .stat-box.pass-c .s-value { color: #2d6a2d; }
        .stat-box.impr-c .s-value { color: #856404; }
        .charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 20px; }
        .chart-box { background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 20px; }
        .chart-box h3 { font-size: 14px; font-weight: 700; color: #1a3c6e; margin-bottom: 4px; }
        .chart-box p { font-size: 12px; color: #888; margin-bottom: 14px; }
        .chart-wrap { position: relative; height: 240px; }
        .legend { display: flex; gap: 16px; margin-bottom: 10px; }
        .legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #555; }
        .legend-dot { width: 12px; height: 12px; border-radius: 3px; }
        .card { background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); overflow: hidden; }
        .card-head { background: #1a3c6e; color: white; padding: 14px 20px; font-size: 15px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f0f4fa; color: #1a3c6e; font-size: 12px; font-weight: 700; padding: 11px 14px; text-align: left; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 2px solid #d0daea; }
        td { padding: 12px 14px; font-size: 14px; color: #333; border-bottom: 1px solid #f0f0f0; }
        tr:hover td { background: #f8faff; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .pass { background: #e6f4ea; color: #2d6a2d; }
        .fail { background: #fdecea; color: #a61c00; }
        .pct { font-weight: 700; }
        .pct.high { color: #2d6a2d; }
        .pct.mid  { color: #856404; }
        .pct.low  { color: #a61c00; }
        .trend-up   { color: #2d6a2d; font-weight: 700; }
        .trend-down { color: #a61c00; font-weight: 700; }
        .trend-same { color: #888; }
        .empty-state { text-align: center; padding: 60px 20px; color: #999; background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .empty-state .icon { font-size: 48px; margin-bottom: 12px; }
        .hidden { display: none; }
    </style>
</head>
<body>
<div class="header">
    <h1>📊 My Grade Progress</h1>
    <span>Student View · EduTeam</span>
</div>
<div class="container">
    <div class="search-card">
        <label>Student ID:</label>
        <input type="number" id="sid-input" placeholder="e.g. 203" onkeydown="if(event.key==='Enter') loadStudent()">
        <button onclick="loadStudent()">View My Grades</button>
        <span class="hint" id="hint"></span>
    </div>

    <div id="content" class="hidden">
        <div class="stat-row">
            <div class="stat-box">
                <div class="s-label">Student ID</div>
                <div class="s-value" id="c-sid">—</div>
            </div>
            <div class="stat-box">
                <div class="s-label">Total Courses</div>
                <div class="s-value" id="c-courses">—</div>
            </div>
            <div class="stat-box pass-c">
                <div class="s-label">Courses Passed</div>
                <div class="s-value" id="c-pass">—</div>
            </div>
            <div class="stat-box impr-c">
                <div class="s-label">Avg Improvement</div>
                <div class="s-value" id="c-impr">—</div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-box">
                <h3>Mid-Term vs Final Term by Course</h3>
                <p>Your scores across all courses</p>
                <div class="legend">
                    <div class="legend-item"><div class="legend-dot" style="background:#f59e0b"></div> Mid-Term</div>
                    <div class="legend-item"><div class="legend-dot" style="background:#1a3c6e"></div> Final Term</div>
                </div>
                <div class="chart-wrap"><canvas id="barChart"></canvas></div>
            </div>
            <div class="chart-box">
                <h3>Progress Trend</h3>
                <p>Mid vs Final per course</p>
                <div class="chart-wrap"><canvas id="lineChart"></canvas></div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">📋 Course-wise Breakdown</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Course</th><th>Mid-Term</th><th>Final Term</th>
                        <th>Total</th><th>Percentage</th><th>Change</th><th>Result</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>
    </div>

    <div id="empty-state">
        <div class="empty-state">
            <div class="icon">🎓</div>
            <p>Enter your Student ID above to view your grade progress</p>
        </div>
    </div>
</div>

<script>
    let barChart, lineChart;

    const urlSid = new URLSearchParams(window.location.search).get('student_id');
    if (urlSid) { document.getElementById('sid-input').value = urlSid; loadStudent(); }

    function loadStudent() {
        const sid = document.getElementById('sid-input').value.trim();
        if (!sid) { document.getElementById('hint').textContent = '⚠ Please enter a Student ID'; return; }
        document.getElementById('hint').textContent = 'Loading...';

        fetch('list.php?student_id=' + sid)
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    document.getElementById('hint').textContent = '❌ No grades found for ID ' + sid;
                    document.getElementById('content').classList.add('hidden');
                    document.getElementById('empty-state').style.display = 'block';
                    return;
                }
                document.getElementById('hint').textContent = '✓ Found ' + data.length + ' course(s)';
                document.getElementById('content').classList.remove('hidden');
                document.getElementById('empty-state').style.display = 'none';
                renderStats(data, sid);
                renderCharts(data);
                renderTable(data);
            });
    }

    function renderStats(data, sid) {
        const passed  = data.filter(g => g.is_passed == 1).length;
        const avgImpr = (data.reduce((s,g) => s + (parseFloat(g.final_term) - parseFloat(g.mid_term)), 0) / data.length).toFixed(1);
        document.getElementById('c-sid').textContent     = sid;
        document.getElementById('c-courses').textContent = data.length;
        document.getElementById('c-pass').textContent    = passed + '/' + data.length;
        document.getElementById('c-impr').textContent    = (avgImpr > 0 ? '+' : '') + avgImpr;
    }

    function renderCharts(data) {
        const courses = data.map(g => g.course_id);
        const mid     = data.map(g => parseFloat(g.mid_term));
        const final   = data.map(g => parseFloat(g.final_term));

        if (barChart)  barChart.destroy();
        if (lineChart) lineChart.destroy();

        barChart = new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: { labels: courses, datasets: [
                { label: 'Mid-Term',   data: mid,   backgroundColor: 'rgba(245,158,11,0.8)', borderRadius: 6 },
                { label: 'Final Term', data: final, backgroundColor: 'rgba(26,60,110,0.8)',  borderRadius: 6 }
            ]},
            options: { responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { min: 0, max: 100 } }
            }
        });

        lineChart = new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: { labels: courses, datasets: [
                { label: 'Mid-Term',   data: mid,   borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.1)', tension: 0.4, fill: true, pointRadius: 5, pointBackgroundColor: '#f59e0b' },
                { label: 'Final Term', data: final, borderColor: '#1a3c6e', backgroundColor: 'rgba(26,60,110,0.1)',  tension: 0.4, fill: true, pointRadius: 5, pointBackgroundColor: '#1a3c6e' }
            ]},
            options: { responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } } },
                scales: { x: { grid: { display: false } }, y: { min: 0, max: 100 } }
            }
        });
    }

    function renderTable(data) {
        document.getElementById('tableBody').innerHTML = data.map((g, i) => {
            const diff = parseFloat(g.final_term) - parseFloat(g.mid_term);
            const pct  = parseFloat(g.percentage);
            const pctC = pct >= 70 ? 'high' : pct >= 50 ? 'mid' : 'low';
            const trend = diff > 0 ? `<span class="trend-up">▲ +${diff.toFixed(1)}</span>`
                        : diff < 0 ? `<span class="trend-down">▼ ${diff.toFixed(1)}</span>`
                        : `<span class="trend-same">— 0</span>`;
            return `<tr>
                <td>${i+1}</td>
                <td><strong>${g.course_id}</strong></td>
                <td>${parseFloat(g.mid_term).toFixed(2)}</td>
                <td>${parseFloat(g.final_term).toFixed(2)}</td>
                <td>${parseFloat(g.total_grade).toFixed(2)}</td>
                <td><span class="pct ${pctC}">${pct.toFixed(2)}%</span></td>
                <td>${trend}</td>
                <td><span class="badge ${g.is_passed==1?'pass':'fail'}">${g.is_passed==1?'Passed':'Failed'}</span></td>
            </tr>`;
        }).join('');
    }
</script>
</body>
</html>
