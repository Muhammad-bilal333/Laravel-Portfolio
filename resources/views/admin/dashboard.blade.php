<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/FUSSTLogo.jpg') }}">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Sidebar Styles */
        .sidebar {
            height: auto;
            background: linear-gradient(to bottom, #3C9AA5, #23546B);
            color: white;
            padding-top: 30px;
            min-height: 100vh;
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
            white-space: nowrap;
        }
        .btn-sidebar.active {
            background: linear-gradient(to right, #3C9AA5, #23546B);
        }
        .mainbar{

        }

        /* Navbar and Page Layout */
        body {
            background-color: #E2ECF2 ;
        }
        .navbar {
            background: linear-gradient(to bottom, #23546B, #3C9AA5);
        }
        .logo {
            max-width: 300px;
            border-radius: 30px;
            mix-blend-mode: color-burn;
        }
        form {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px; /* Rounded corners for the form */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            margin-top: 50px;
        }
        label {
            font-weight: bold;
            color: black;
        }
        input[type="text"], input[type="email"], 
        input[type="password"],  select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        input[type="submit"] {
            background-color: #3C9AA5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #23546B;
        }
        .icon-container {
            display: flex;
            flex-direction: row;
            align-items: flex-end; /* Aligns icons to the right */
            margin-left: auto; /* Pushes the icons to the far right */
        }
        .icon-container a {
            color: white;
            font-size: 24px;
            margin: 0 10px;
        }
        .mobilemenu{
                display: none;
        }




          @media (max-width: 768px) {
            .logo {
            max-width: 200px;
            border-radius: 30px;
            mix-blend-mode: color-burn;
            }
            .sidebar{
                padding-top: 0;
                height: auto;
            }
            .text-center{
                font-size: 1.5rem;
            }
        }
        .dashboard-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(60, 154, 165, 0.10), 0 1.5px 4px rgba(35, 84, 107, 0.10);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            min-height: 180px;
        }
        .dashboard-card:hover {
            transform: translateY(-8px) scale(1.04);
            box-shadow: 0 8px 32px rgba(60, 154, 165, 0.18), 0 3px 8px rgba(35, 84, 107, 0.15);
            background: linear-gradient(120deg, #e2ecf2 60%, #d0f0f7 100%);
        }
        @media (max-width: 991px) {
            .dashboard-card-link {
                flex: 0 0 90%;
                max-width: 90%;
            }
        }
        @media (max-width: 767px) {
            .dashboard-card-link {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        .stat-card {
            min-width: 180px;
            max-width: 220px;
        }
        .stat-card-inner {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(60, 154, 165, 0.10);
            transition: box-shadow 0.2s;
        }
        .stat-card-inner:hover {
            box-shadow: 0 6px 24px rgba(60, 154, 165, 0.18);
        }
        .stat-title {
            font-size: 1.1rem;
            color: #23546B;
            font-weight: 500;
        }
        .stat-value {
            font-size: 2rem;
            color: #3C9AA5;
            font-weight: bold;
        }
        .recent-activity-card {
            margin-bottom: 40px;
        }
        .recent-activity-card ul li {
            font-size: 1.08rem;
            color: #23546B;
            border-left: 3px solid #3C9AA5;
            padding-left: 12px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .recent-activity-card ul li:last-child {
            margin-bottom: 0;
        }
        .timeline {
            position: relative;
        }
        .timeline-line {
            pointer-events: none;
        }
        .timeline-item:last-child .timeline-dot {
            box-shadow: 0 2px 8px rgba(60,154,165,0.10), 0 0 0 4px #e2ecf2;
        }
        @media (max-width: 600px) {
            .recent-activity-card { padding: 1.2rem !important; }
            .timeline-dot { width: 28px !important; height: 28px !important; font-size: 1em !important; }
            .timeline-item { margin-bottom: 1.2rem !important; }
        }
    </style>
</head>

<body>

<!-- Navbar -->
<div class="container-fluid p-0">
    <nav class="col-md-12 col-lg-12 navbar ">
        <div class="container-fluid">
            <img src="{{ asset('img/logo_wn.png') }}" alt="FUI Logo" class="logo img-fluid" >
            <div class="icon-container">
                <a href="{{ route('admin.dashboard') }}" title="Home">
                    <i class="fas fa-home"></i>
                </a>
                <a href="https://fusst.fui.edu.pk/" title="Information">
                    <i class="fas fa-info-circle"></i>
                </a>
                <a href="mailto:fusst@fui.edu.pk" title="fusst@fui.edu.pk" data-toggle="tooltip" data-placement="left">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="row m-0" >
        <!-- Sidebar Section -->
        <div class="col-md-3 col-lg-2 sidebar">
            <button class="btn btn-sidebar font-weight-bold" data-toggle="collapse" style="color: white" data-target="#facultyMenu">Faculty Management</button>
            <div id="facultyMenu" class="collapse">
                <a href="{{ route('faculty.list') }}" class="d-block pl-4 py-1">Faculty List</a>
                <a href="{{ route('add.faculty') }}" class="d-block pl-4 py-1">Add Faculty</a>
            </div>

            <!-- Student Management Section -->
            <button class="btn btn-sidebar font-weight-bold" data-toggle="collapse" style="color: white" data-target="#studentMenu">Student Management</button>
            <div id="studentMenu" class="collapse">
                <a href="{{ route('student.list') }}" class="d-block pl-4 py-1">Student List</a>
                <a href="{{ route('add.student') }}" class="d-block pl-4 py-1">Add Student</a>
            </div>

            <button class="btn btn-sidebar font-weight-bold" data-toggle="collapse" style="color: white" data-target="#qecMenu">QEC Management</button>
            <div id="qecMenu" class="collapse">
                <a href="{{ route('QualityEnhancementCell.list') }}" class="d-block pl-4 py-1">QEC List</a>
                <a href="{{ route('add.QualityEnhancementCell') }}" class="d-block pl-4 py-1">Add QEC</a>
            </div>

            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <button class="btn btn-sidebar font-weight-bold" style="color: white" onclick="document.getElementById('logout-form').submit();">
                Sign out
            </button>
        </div>

        <!-- Main Content Section -->
        <div class="col-md-9 col-lg-10 mainbar">
            <div >
                <h1 class="mt-4 text-center">Welcome To The Admin Dashboard</h1>
                <!-- Statistics Cards Row -->
                <div class="row justify-content-center mt-4" style="gap: 20px;">
                    <div class="stat-card col-md-2 col-sm-4 mb-3 p-0 d-flex align-items-center justify-content-center">
                        <div class="stat-card-inner w-100 text-center p-3">
                            <i class="fas fa-chalkboard-teacher fa-2x mb-2" style="color: #3C9AA5;"></i>
                            <div class="stat-title">Total Faculty</div>
                            <div class="stat-value">{{ $totalFaculty }}</div>
                        </div>
                    </div>
                    <div class="stat-card col-md-2 col-sm-4 mb-3 p-0 d-flex align-items-center justify-content-center">
                        <div class="stat-card-inner w-100 text-center p-3">
                            <i class="fas fa-user-graduate fa-2x mb-2" style="color: #3C9AA5;"></i>
                            <div class="stat-title">Total Students</div>
                            <div class="stat-value">{{ $totalStudents }}</div>
                        </div>
                    </div>
                    <div class="stat-card col-md-2 col-sm-4 mb-3 p-0 d-flex align-items-center justify-content-center">
                        <div class="stat-card-inner w-100 text-center p-3">
                            <i class="fas fa-award fa-2x mb-2" style="color: #3C9AA5;"></i>
                            <div class="stat-title">Total QEC</div>
                            <div class="stat-value">{{ $totalQEC }}</div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-5" style="gap: 30px;">
                    <!-- Faculty Management -->
                    <a href="{{ route('faculty.list') }}" class="dashboard-card-link col-md-3 col-sm-6 mb-4 p-0" style="text-decoration: none;">
                        <div class="dashboard-card h-100 p-4 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-chalkboard-teacher fa-3x mb-3" style="color: #23546B;"></i>
                            <h5 class="card-title font-weight-bold text-center" style="color: #23546B;">Faculty Management</h5>
                            <p class="card-text text-center" style="color: #23546B;">Manage Faculty Effectively.</p>
                        </div>
                    </a>
                    <!-- Student Management -->
                    <a href="{{ route('student.list') }}" class="dashboard-card-link col-md-3 col-sm-6 mb-4 p-0" style="text-decoration: none;">
                        <div class="dashboard-card h-100 p-4 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-user-graduate fa-3x mb-3" style="color: #23546B;"></i>
                            <h5 class="card-title font-weight-bold text-center" style="color: #23546B;">Student Management</h5>
                            <p class="card-text text-center" style="color: #23546B;">Manage Students Effectively.</p>
                        </div>
                    </a>
                    <!-- QEC Management -->
                    <a href="{{ route('QualityEnhancementCell.list') }}" class="dashboard-card-link col-md-3 col-sm-6 mb-4 p-0" style="text-decoration: none;">
                        <div class="dashboard-card h-100 p-4 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-award fa-3x mb-3" style="color: #23546B;"></i>
                            <h5 class="card-title font-weight-bold text-center" style="color: #23546B;">QEC Management</h5>
                            <p class="card-text text-center" style="color: #23546B;">Quality Enhancement Cell.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".mobilemenu").click(function() {
            $(".sidebar").toggleClass("active"); // Toggle sidebar visibility
            $(this).toggleClass("fa-bars fa-times"); // Toggle menu icon (bars ↔ close)
        });
    });
</script>
</body>
</html>





























