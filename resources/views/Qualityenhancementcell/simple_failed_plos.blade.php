<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QEC - Course Review Reports</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/FUSSTLogo.jpg') }}">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .sidebar {
            height: 100vh;
            background: linear-gradient(to bottom, #3C9AA5, #23546B);
            color: white;
            padding-top: 70px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            border: none;
            text-align: left;
            transition: background-color 0.3s, transform 0.2s;
        }
        .sidebar a:hover {
            text-decoration: underline;
            transform: scale(1.05);
        }
        .btn-sidebar {
            width: 100%;
            margin-bottom: 10px;
            text-align: left;
            background: #23546B;
            padding-top: 10px;
        }
        .btn-sidebar.active {
            background: linear-gradient(to right, #3C9AA5, #23546B);
        }
        body {
            background-color: #E2ECF2;
        }
        .navbar {
            background: linear-gradient(to bottom, #23546B, #3C9AA5);
        }
        .logo {
            max-width: 300px;
            border-radius: 30px;
            mix-blend-mode: color-burn;
        }
        .icon-container {
            display: flex;
            flex-direction: row;
            align-items: flex-end;
            margin-left: auto;
        }
        .icon-container a {
            color: white;
            font-size: 24px;
            margin: 0 10px;
        }
        @media (max-width: 768px) {
            .logo { max-width: 200px; }
            .sidebar{ padding-top: 0; height: auto; }
            .text-center{ font-size: 1.5rem; }
        }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <nav class="col-md-12 col-lg-12 navbar">
        <div class="container-fluid">
            <img src="{{ asset('img/logo_wn.png') }}" alt="FUI Logo" class="logo img-fluid">
            <div class="icon-container">
                <a href="{{ route('Qualityenhancementcell.dashboard') }}" title="Home">
                    <i class="fas fa-home"></i>
                </a>
                <a href="https://fusst.fui.edu.pk/" title="Information" target="_blank">
                    <i class="fas fa-info-circle"></i>
                </a>
                <a href="mailto:fusst@fui.edu.pk" title="fusst@fui.edu.pk">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="row m-0">
        <!-- Sidebar Section -->
        <div class="col-md-3 col-lg-2 sidebar">
            <a href="{{ route('Qualityenhancementcell.dashboard') }}" class="btn btn-sidebar font-weight-bold mb-2" style="color: white; font-size: 1.1rem;">Dashboard</a>
            <form id="logout-form" method="POST" action="{{ route('qec.logout') }}" style="display: none;">@csrf</form>
            <button class="btn btn-sidebar font-weight-bold" style="color: white; width: 100%;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</button>
        </div>

        <!-- Main Content Section -->
        <div class="col-md-9 col-lg-10 mainbar">
            <div class="container mt-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Course Review Reports</h4>
                        <div>
                            <button class="btn btn-light btn-sm" onclick="refreshCRRData()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search courses...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="searchCourses()">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="crrTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Lecturer</th>
                                        <th>Total Students</th>
                                        <th>Failed PLOs</th>
                                        <th>Last Updated</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="crrTableBody">
                                    <!-- Data will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Cards -->
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total CRRs</h6>
                                <h3 class="mb-0" id="totalCRRs">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h6 class="card-title">Pending Review</h6>
                                <h3 class="mb-0" id="pendingCRRs">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Reviewed</h6>
                                <h3 class="mb-0" id="reviewedCRRs">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6 class="card-title">Needs Attention</h6>
                                <h3 class="mb-0" id="attentionCRRs">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Function to fetch CRR data
    function fetchCRRData() {
        $.ajax({
            url: '/qec/fetch-crr-data', // You'll need to create this route
            method: 'GET',
            success: function(response) {
                updateCRRTable(response.crrs);
                updateStats(response.stats);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching CRR data:', error);
            }
        });
    }

    // Function to update the CRR table
    function updateCRRTable(crrs) {
        const tbody = $('#crrTableBody');
        tbody.empty();

        crrs.forEach(crr => {
            const row = `
                <tr>
                    <td>${crr.course_code}</td>
                    <td>${crr.course_name}</td>
                    <td>${crr.lecturer}</td>
                    <td>${crr.total_students}</td>
                    <td>${crr.failed_plos}</td>
                    <td>${crr.last_updated}</td>
                    <td><span class="badge badge-${getStatusBadgeClass(crr.status)}">${crr.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" title="View CRR" onclick="viewCRR('${crr.id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-success" title="Download" onclick="downloadCRR('${crr.id}')">
                            <i class="fas fa-download"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Function to update stats
    function updateStats(stats) {
        $('#totalCRRs').text(stats.total);
        $('#pendingCRRs').text(stats.pending);
        $('#reviewedCRRs').text(stats.reviewed);
        $('#attentionCRRs').text(stats.needs_attention);
    }

    // Function to get badge class based on status
    function getStatusBadgeClass(status) {
        switch(status.toLowerCase()) {
            case 'pending review': return 'warning';
            case 'reviewed': return 'success';
            case 'needs attention': return 'danger';
            default: return 'secondary';
        }
    }

    // Function to refresh CRR data
    function refreshCRRData() {
        fetchCRRData();
    }

    // Function to search courses
    function searchCourses() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        $('#crrTableBody tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchTerm));
        });
    }

    // Function to view CRR
    function viewCRR(crrId) {
        window.location.href = `/qec/view-crr/${crrId}`;
    }

    // Function to download CRR
    function downloadCRR(crrId) {
        window.location.href = `/qec/download-crr/${crrId}`;
    }

    // Initial load
    $(document).ready(function() {
        fetchCRRData();
        // Refresh data every 30 seconds
        setInterval(fetchCRRData, 30000);
    });
</script>
</body>
</html> 