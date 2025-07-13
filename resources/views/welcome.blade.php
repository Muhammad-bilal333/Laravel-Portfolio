@include('layouts.head')
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>

    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #E2ECF2;
        }
        .main-container {
            background-color: #e3edf7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 10px;
            display: flex;
            flex-wrap: wrap;
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .left-panel {
            background: linear-gradient(to bottom, #033649, #005f73);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex: 1;
        }
        .left-panel img {
            max-width: 180px;
            margin-top: 20px;
            opacity: 0.8;
            max-width: 60%;
            border-radius: 10px;
            mix-blend-mode: color-burn;
        }
        .right-panel {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
            width: 50%;
        }
        .form-check {
            margin-bottom: 15px;
        }
        .form-check-input {
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .left-panel {
                padding: 5px;
            }
            .right-panel {
                padding: 2px;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="login-container">
            <div class="col-md-6 left-panel">
                <h2 class="title">Foundation University<br>School of Science and Technology</h2>
                <img src="{{ asset('img/FUSSTLogo.jpg') }}" alt="University Logo">
            </div>
            <div class="col-md-6 right-panel">
                <h2 class="mb-4 text-center">Login</h2>
                <form>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="user_type" id="admin" onclick="redirectTo('/admin/login')">
                        <label class="form-check-label" for="admin">Admin</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="user_type" id="faculty" onclick="redirectTo('/faculty/login')">
                        <label class="form-check-label" for="faculty">Faculty</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="user_type" id="student" onclick="redirectTo('/login')">
                        <label class="form-check-label" for="student">Student</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="user_type" id="qec" onclick="redirectTo('/qec/login')">
                        <label class="form-check-label" for="qec">QEC</label>
                    </div>
                </form>
                <script>
                    function redirectTo(url) {
                        window.location.href = url;
                    }
                </script>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
{{-- <!DOCTYPE html>
<html>
<head>
    <title>API Charts Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        canvas {
            max-width: 800px;
            margin: 40px auto;
            display: block;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-top: 40px;
        }
        .chart-container {
            width: 80%;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <!-- 1. Bar Chart for Emails, Phones, etc -->
    <h2>1. Source Summary (Bar Chart)</h2>
    <div class="chart-container">
        <canvas id="barChart"></canvas>
    </div>

    <!-- 2. Donut Chart for Source Percentages -->
    <h2>2. Source Percentages (Donut Chart)</h2>
    <div class="chart-container">
        <canvas id="donutChart"></canvas>
    </div>

    <!-- 3. Grouped Bar Chart for Each Source (3 Stats) -->
    <h2>3. Source Details (Grouped Bar Chart)</h2>
    <div class="chart-container">
        <canvas id="groupedBarChart"></canvas>
    </div>

    <script>
        // 1. Bar Chart: Summary Stats (Emails, Phones, etc)
        const barCtx = document.getElementById('barChart').getContext('2d');
        
        // Data from first API response (source_summary)
        const sourceSummaryData = [
            { source: "Yellow Pages", emails_count: 0, phone_numbers_count: 59197, total_count: 59204 },
            { source: "Google", emails_count: 0, phone_numbers_count: 29068, total_count: 29068 },
            { source: "Merchant", emails_count: 0, phone_numbers_count: 4833, total_count: 4833 },
            { source: "otherlinks", emails_count: 4, phone_numbers_count: 589, total_count: 600 },
            { source: "houzzapp", emails_count: 0, phone_numbers_count: 57, total_count: 57 }
        ];
        
        const barLabels = sourceSummaryData.map(item => item.source);
        
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [
                    {
                        label: 'Emails',
                        data: sourceSummaryData.map(item => item.emails_count),
                        backgroundColor: 'rgba(255, 99, 132, 0.7)'
                    },
                    {
                        label: 'Phone Numbers',
                        data: sourceSummaryData.map(item => item.phone_numbers_count),
                        backgroundColor: 'rgba(54, 162, 235, 0.7)'
                    },
                    {
                        label: 'Total Count',
                        data: sourceSummaryData.map(item => item.total_count),
                        backgroundColor: 'rgba(75, 192, 192, 0.7)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Source Summary Statistics'
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 2. Donut Chart: Source Percentages
        const donutCtx = document.getElementById('donutChart').getContext('2d');
        
        // Data from second API response (source_percentages)
        const sourcePercentagesData = [
            { source: "Yellow Pages", source_percentage: "63.14", total_count: 59204 },
            { source: "Google", source_percentage: "31.00", total_count: 29068 },
            { source: "Merchant", source_percentage: "5.15", total_count: 4833 },
            { source: "otherlinks", source_percentage: "0.64", total_count: 600 },
            { source: "houzzapp", source_percentage: "0.06", total_count: 57 },
            { source: "Loopnet", source_percentage: "0.00", total_count: 0 },
            { source: "Glassdoor", source_percentage: "0.00", total_count: 0 },
            { source: "whitepages", source_percentage: "0.00", total_count: 0 },
            { source: "WhereOrg", source_percentage: "0.00", total_count: 0 }
        ];
        
        // Filter out sources with 0 percentage for cleaner visualization
        const filteredPercentages = sourcePercentagesData.filter(item => parseFloat(item.source_percentage) > 0);
        
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: filteredPercentages.map(item => `${item.source} (${item.source_percentage}%)`),
                datasets: [{
                    data: filteredPercentages.map(item => parseFloat(item.source_percentage)),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
                        '#8AC24A', '#F06292', '#7986CB'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Source Distribution by Percentage'
                    },
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value}%`;
                            }
                        }
                    }
                }
            }
        });

        // 3. Grouped Bar Chart: Total, Emails, Phones per Source
        const groupedCtx = document.getElementById('groupedBarChart').getContext('2d');
        
        // Data from first API response (source_summary) for this chart
        const groupedData = sourceSummaryData;
        const sourceLabels = groupedData.map(item => item.source);
        
        new Chart(groupedCtx, {
            type: 'bar',
            data: {
                labels: sourceLabels,
                datasets: [
                    {
                        label: 'Total Records',
                        data: groupedData.map(item => item.total_count),
                        backgroundColor: '#42a5f5'
                    },
                    {
                        label: 'Emails',
                        data: groupedData.map(item => item.emails_count),
                        backgroundColor: '#66bb6a'
                    },
                    {
                        label: 'Phone Numbers',
                        data: groupedData.map(item => item.phone_numbers_count),
                        backgroundColor: '#ffa726'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Detailed Stats by Source'
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html> --}}