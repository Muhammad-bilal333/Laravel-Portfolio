{{-- <div class="sidebar">

    <img src="{{ asset('img/logo_wn.png') }}" alt="FUI Logo" class="logo img-fluid" >
    <button class="btn btn-sidebar font-weight-bold" data-toggle="facultyMenu">Faculty Management</button>
    <div id="facultyMenu" class="submenu" style="display: none;">
        <a href="{{ route('add.faculty') }}" class="d-block pl-4 py-1">Faculty List</a>
        <a href="{{ route('faculty.list') }}" class="d-block pl-4 py-1">Add Faculty</a>
    </div>
    
    <button class="btn btn-sidebar font-weight-bold" data-toggle="studentMenu">Student Management</button>
    <div id="studentMenu" class="submenu" style="display: none;">
        <a href="{{ route('add.student') }}" class="d-block pl-4 py-1">Student List</a>
        <a href="{{ route('student.list') }}" class="d-block pl-4 py-1">Add Student</a>
    </div>

    <button class="btn btn-sidebar font-weight-bold" data-toggle="qecMenu">QEC Management</button>
    <div id="qecMenu" class="submenu" style="display: none;">
        <a href="{{ route('add.QualityEnhancementCell') }}" class="d-block pl-4 py-1">QEC List</a>
        <a href="{{ route('QualityEnhancementCell.list') }}" class="d-block pl-4 py-1">Add QEC</a>
    </div>
    
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-sidebar font-weight-bold">Sign out</button>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('.btn-sidebar').click(function(){
            var target = $(this).data('toggle');
            if (target) {
                $('#' + target).slideToggle();
            }
        });
    });
</script> --}}




@include('layouts.head')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>


    <style>
        .sidebar {
            height: 100vh;
            background: linear-gradient(to bottom, #3C9AA5, #23546B);
            color: white;
            padding-top: 70px;
            position: fixed;
            width: 15%;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
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
            background: #23546B;
            font-weight: bold;
            text-align: left;
            color: white;
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
            align-items: center;
            margin-left: auto;
        }
        .icon-container a {
            color: white;
            font-size: 24px;
            margin: 0 10px;
        }
        .main-content {
            margin-left: 16%;
            padding: 20px;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <img src="{{ asset('img/logo_wn.png') }}" alt="FUI Logo" class="logo img-fluid" >
        <div class="icon-container">
            <a href="main_page.php" title="Home">
                <i class="fas fa-home"></i>
            </a>
            <a href="https://fusst.fui.edu.pk/" title="Information">
                <i class="fas fa-info-circle"></i>
            </a>
            <a href="#" title=" fusst@fui.edu.pk" data-toggle="tooltip" data-placement="left">
                <i class="fas fa-envelope"></i>
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="sidebar">
            <button class="btn btn-sidebar font-weight-bold" data-toggle="facultyMenu">Faculty Management</button>
            <div id="facultyMenu" class="submenu" style="display: none;">
                <a href="{{ route('add.faculty') }}" class="d-block pl-4 py-1">Faculty List</a>
                <a href="{{ route('faculty.list') }}" class="d-block pl-4 py-1">Add Faculty</a>
            </div>
            
            <button class="btn btn-sidebar font-weight-bold" data-toggle="studentMenu">Student Management</button>
            <div id="studentMenu" class="submenu" style="display: none;">
                <a href="{{ route('add.student') }}" class="d-block pl-4 py-1">Student List</a>
                <a href="{{ route('student.list') }}" class="d-block pl-4 py-1">Add Student</a>
            </div>

            <button class="btn btn-sidebar font-weight-bold" data-toggle="qecMenu">QEC Management</button>
            <div id="qecMenu" class="submenu" style="display: none;">
                <a href="{{ route('add.QualityEnhancementCell') }}" class="d-block pl-4 py-1">QEC List</a>
                <a href="{{ route('QualityEnhancementCell.list') }}" class="d-block pl-4 py-1">Add QEC</a>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sidebar font-weight-bold">Sign out</button>
            </form>
        </div>

        <div class="main-content">
            <h1 class="mt-4">Welcome to the Admin Dashboard</h1>
            <p>Select options from the sidebar to manage faculty and student.</p>
            <div class="row">
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Faculty Management</h5>
                            <p class="card-text">Manage Faculty Effectively. </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Student Management</h5>
                            <p class="card-text">Manage Students Effectively.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Quality Enhancement Cell Management</h5>
                            <p class="card-text">Manage Quality Enhancement Cell Effectively.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.btn-sidebar').click(function(){
            var target = $(this).data('toggle');
            if (target) {
                $('#' + target).slideToggle();
            }
        });
    });
</script>
</body>
</html>
