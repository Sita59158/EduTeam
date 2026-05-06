<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Statistics</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; min-height: 100vh; }

        .header { background: #1a3c6e; color: white; padding: 16px 30px; display: flex; align-items: center; justify-content: space-between; }
        .header h1 { font-size: 20px; font-weight: 600; }
        .header span { font-size: 13px; opacity: 0.8; }

        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }

        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-box { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-box label { font-size: 12px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: 0.04em; display: block; margin-bottom: 8px; }
        .stat-box span { font-size: 32px; font-weight: 700; }
        .s-blue { color: #1a3c6e; }
        .s-green { color: #0f6e56; }
        .s-red { color: #a61c00; }

        .card { background: white; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 24px; }
        .card-head { background: #1a3c6e; color: white; padding: 14px 20px; font-size: 15px; font-weight: 600; }
        .card-body { padding: 30px; display: flex; justify-content: center; }

        .chart-wrap { width: 380px; height: 380px; position: relative; }

        .legend { display: flex; justify-content: center; gap: 30px; margin-top: 20px; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #333; }
        .legend-dot { width: 14px; height: 14px; border-radius: 50%; }

        .nav-row { display: flex; gap: 10px; margin-bottom: 20px; }
        .nav-btn { padding: 8px 18px; border-radius: 6px; border: 1.5px solid #1a3c6e; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; color: #1a3c6e; background: white; transition: all 0.2s; }
        .nav-btn:hover { background: #1a3c6e; color: white; }

        .empty { text-align: center; padding: 60px; color: #999; font-size: 14px; }
        .no-data { text-align: center; padding: 60px 20px; }
        .no-data .icon { font-size: 48px; margin-bottom: 12px; }
        .no-data p { font-size: 14px; color: #999; }
    </style>
</head>
<body>

<div class="header">
    <h1>📊 Grade Statistics</h1>
    <span>Charts · EduTeam</span>
</div>

<div class="container">

    <div class="nav-row">
        <a href="teacher_grades.php" class="nav-btn">← Teacher View</a>
        <a href="student_grades.php" class="nav-btn">Student View</a>
    </div>

    <!-- Summary Stats -->
    <div class="stats-grid">
        <div class="stat-box">
            <label>Total Records</label>
            <span class="s-blue" id="totalCount">—</span>
        </div>
        <div class="stat-box">
            <label>Total Passed</label>
            <span class="s-green" id="passCount">—</span>
        </div>
        <div class="stat-box">
            <label>Total Failed</label>
            <span class="s-red" id="failCount">—</span>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="card">
        <div class="card-head">Pass vs Fail — All Students</div>
        <div class="card-body" style="flex-direction: column; align-items: center;">
            <div class="chart-wrap">
                <canvas id="pieChart"></canvas>
            </div>
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-dot" style="background:#0f6e56;"></div>
                    <span id="legendPass">Passed</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#e74c3c;"></div>
                    <span id="legendFail">Failed</span>
                </div>
            </div>
            <div class="no-data" id="noData" style="display:none;">
                <div class="icon">📭</div>
                <p>No grade data available yet.<br>Add some grades from the Teacher View first.</p>
            </div>
        </div>
    </div>

</div>

<script>
fetch('list.php')
    .then(r => r.json())
    .then(data => {
        const total  = data.length;
        const passed = data.filter(g => g.is_passed == 1).length;
        const failed = total - passed;

        document.getElementById('totalCount').textContent = total;
        document.getElementById('passCount').textContent  = passed;
        document.getElementById('failCount').textContent  = failed;

        if (total === 0) {
            document.getElementById('noData').style.display = 'block';
            document.getElementById('pieChart').style.display = 'none';
            return;
        }

        const passPct = ((passed / total) * 100).toFixed(1);
        const failPct = ((failed / total) * 100).toFixed(1);

        document.getElementById('legendPass').textContent = `Passed — ${passed} student${passed !== 1 ? 's' : ''} (${passPct}%)`;
        document.getElementById('legendFail').textContent = `Failed — ${failed} student${failed !== 1 ? 's' : ''} (${failPct}%)`;

        const ctx = document.getElementById('pieChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Passed', 'Failed'],
                datasets: [{
                    data: [passed, failed],
                    backgroundColor: ['#0f6e56', '#e74c3c'],
                    borderColor: ['#ffffff', '#ffffff'],
                    borderWidth: 3,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed;
                                const pct = ((val / total) * 100).toFixed(1);
                                return ` ${context.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
</body>
</html>
