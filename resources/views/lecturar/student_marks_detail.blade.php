{{-- student_marks_detail.blade --}}
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
        }  .faculty-table {
            width: 100%;
            margin-top: 20px;
            background-color: #ffffff;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .faculty-table th, .faculty-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .faculty-table th {
            background-color: #23546B;
            color: #ffffff;
            font-weight: bold;
        }
        .faculty-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .faculty-table tr:hover {
            background-color: #d3eaf2;
            cursor: pointer;
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
    </style>
</head>

<body>

<!-- Navbar -->
<div class="container-fluid p-0">
    <nav class="col-md-12 col-lg-12 navbar ">
        <div class="container-fluid">
            <img src="{{ asset('img/logo_wn.png') }}" alt="FUI Logo" class="logo img-fluid" >
            <div class="icon-container">
                <a href="admin_main.php">
                    <i class="fas fa-home"></i>
                </a>
                <a href="https://fusst.fui.edu.pk/" title="Information">
                    <i class="fas fa-info-circle"></i>
                </a>
                <a href="#" title="fusst@fui.edu.pk" data-toggle="tooltip" data-placement="left">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    </nav>
    {{-- {{dd($dutyTasks)}} --}}

    <div class="row m-0" >
        <!-- Sidebar Section -->
        <div class="col-md-3 col-lg-2 sidebar">
            <a href="{{ route('lecturar.dashboard') }}" class="d-block btn btn-sidebar font-weight-bold" style="color: white">Home</a>

            <button class="btn btn-sidebar font-weight-bold" data-toggle="collapse" style="color: white" data-target="#facultyMenu">CDF</button>
            <div id="facultyMenu" class="collapse">
                <a href="{{ route('faculty.list') }}" class="d-block pl-4 py-1">Review CDF</a>
            </div>

            {{-- <h3>{{ ucfirst($user->role->name) }} Dashboard</h3> --}}

            <!-- Show duties -->
            {{-- @if($duties->isNotEmpty())
                <div class="duties-section">
                    <button class="btn btn-sidebar font-weight-bold" data-toggle="collapse" style="color: white" data-target="#dutiesMenu">Additional Duties</button>
                    <div id="dutiesMenu" class="collapse">
                        @foreach($duties as $duty)
                                <a href="{{ route('duty.dashboard', $duty->name) }}" class="duty-link">
                                {{ $duty->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif --}}
            @if($duties->isNotEmpty())
            <div class="duties-section">
                @foreach($duties as $duty)
                    <a href="{{ route('duty.dashboard', $duty->name) }}" class="btn btn-sidebar font-weight-bold duty-link" style="color: white">
                        {{$duty->name }}
                    </a>
                @endforeach
            </div>
            @endif

            <!-- Student Management Section -->
            <button class="btn btn-sidebar font-weight-bold" data-toggle="collapse" style="color: white" data-target="#studentMenu">OBE Sheet</button>
            <div id="studentMenu" class="collapse">
                <a href="{{ route('add.QualityEnhancementCell') }}" class="d-block pl-4 py-1">Marks</a>
               <a href="{{ route('QualityEnhancementCell.list') }}" class="d-block pl-4 py-1">Generate CRRC</a>
            </div>

            <form id="logout-form" action="{{ route('faculty.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            
            <button class="btn btn-sidebar font-weight-bold" style="color: white" onclick="document.getElementById('logout-form').submit();">
                Sign out
            </button>
        </div>

        <!-- Main Content Section -->
        <div class="col-md-9 col-lg-10 mainbar">
            @if(trim($__env->yieldContent('content')))
                <div id="dynamicContent">
                    @yield('content')
                </div>
            @else
            @if(session('success'))
                <div  class="alert alert-success mt-4">{{ session('success') }}</div>
            @endif
            <div class="container mt-5">
                @php
                        $studentMarks = $marksDetail->first();
                @endphp
                <div class="d-flex justify-content-between" style="align-items: center;">
                    <h3 class="mb-4">Student Marks</h3>
                    <td><form class="m-0 p-0" style="box-shadow:none; background:none;" method="Post" action="{{route('Add.marks')}}">
                        @csrf
                       <input type="hidden" name="student_id" value="{{ $student_id }}">
                       <input type="hidden" name="course_id" value="{{ $course_id }}">
                       <input type="hidden" name="teacher_id" value="{{ $teacher_id }}">
                       <input type="hidden" name="cource_allocation_id" value="{{ $cource_allocation_id }}">
                       <button type="submit" class="btn btn-primary ">Add Marks </button>
                    </form></td>
                    {{-- <td><a href="{{ route('Add.marks', $studentMarks->student_id) }}" class="btn btn-primary btn-sm">Add Marks</a> --}}
                </div>
                @forelse($marksDetail as $index => $assessment)
                    <div class="card mb-4">
                        <div class="card-header text-white" style="background: #00556c">
                            <strong>Assessment {{ $index + 1 }}</strong> - {{ $assessment->type ?? 'No Course' }}
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>CLO Number</th>
                                        <th>Total Marks</th>
                                        <th>Obtained Marks</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalMarks = 0;
                                        $obtainedMarks = 0;
                                    @endphp
            
                                    @forelse($assessment->marks as $i => $mark)
                                        @php
                                            $totalMarks += $mark->total_marks;
                                            $obtainedMarks += $mark->obtained_marks;
                                        @endphp
                                        <tr class="text-center">
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $mark->clo_number }}</td>
                                            <td>{{ $mark->total_marks }}</td>
                                            <td>{{ $mark->obtained_marks }}</td>
                                            <td>{{ \Carbon\Carbon::parse($mark->created_at)->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No marks recorded.</td>
                                        </tr>
                                    @endforelse
            
                                    {{-- Sum Row --}}
                                    @if(count($assessment->marks))
                                    <tr class="fw-bold text-center" style="background-color: #f2f2f2">
                                        <td colspan="2">Total</td>
                                        <td>{{ $totalMarks }}</td>
                                        <td>{{ $obtainedMarks }}</td>
                                        <td></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning text-center">
                        No Assessments Found.
                    </div>
                @endforelse
            </div>
            @endif
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





























