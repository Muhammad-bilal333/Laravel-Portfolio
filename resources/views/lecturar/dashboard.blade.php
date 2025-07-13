<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $designation }}</title>
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
            min-height: 100vh;
            height: 100%;
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

            {{-- <button class="btn btn-sidebar font-weight-bold" data-toggle="collapse" style="color: white" data-target="#facultyMenu">CDF</button>
            <div id="facultyMenu" class="collapse">
                <a href="{{ route('faculty.list') }}" class="d-block pl-4 py-1">Review CDF</a>
            </div> --}}

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
            {{-- <button class="btn btn-sidebar font-weight-bold" data-toggle="collapse" style="color: white" data-target="#studentMenu">OBE Sheet</button>
            <div id="studentMenu" class="collapse">
                <a href="{{ route('add.QualityEnhancementCell') }}" class="d-block pl-4 py-1">Marks</a>
               <a href="{{ route('QualityEnhancementCell.list') }}" class="d-block pl-4 py-1">Generate CRRC</a>
            </div> --}}

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
                <div id="defaultContent">
                    <h1 class="mt-4 text-center">Welcome to the {{ $designation }} Dashboard</h1>
                    
                    
                    <h3>Current Courses Assigned</h3>
                    @php
                        $CourseAllocations = $CourseAllocations ?? collect();
                    @endphp
                    <table class="faculty-table">
                        <thead>
                            <tr>
                                <th>Course No</th>
                                <th>Course Title</th>
                                <th>Batch</th>
                                <th>Semester</th>
                                <th>Section</th>
                                <th>OBE Sheet</th>
                                <th>CRR Sheet</th>
                                <th>View All</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($CourseAllocations as $Course)
                           {{-- {{dd($CourseAllocations)}} --}}
                                <tr>
                                    <td>{{ $Course->course->code }}</td>
                                    <td>{{ $Course->course->name }}</td>
                                    <td>{{ $Course->batch }}</td>
                                    <td>{{ $Course->course->semester }}</td>
                                    <td>{{ $Course->section }}</td>
                                    {{-- <td>
                                        @if($Course->course->is_lab)
                                            <form action="{{ route('marks.exportlab') }}" method="GET" style="display: inline; background: none; box-shadow: none; padding: 0;">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $Course->course_id }}">
                                                <input type="hidden" name="faculty_id" value="{{ $Course->faculty->user_id ?? '' }}">
                                                <button type="submit" class="btn btn-sm" style="background: #23546B; color: white; border: none; padding: 5px 15px; border-radius: 4px;">
                                                    OBE LAB
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('marks.export') }}" method="GET" style="display: inline; background: none; box-shadow: none; padding: 0;">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $Course->course_id }}">
                                                <input type="hidden" name="faculty_id" value="{{ $Course->faculty->user_id ?? '' }}">
                                                <button type="submit" class="btn btn-sm" style="background: #23546B; color: white; border: none; padding: 5px 15px; border-radius: 4px;">
                                                    OBE
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('marks.export.word') }}" method="GET" style="display: inline; background: none; box-shadow: none; padding: 0;">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $Course->course_id }}">
                                            <input type="hidden" name="faculty_id" value="{{ $Course->faculty->user_id ?? '' }}">
                                            <button type="submit" class="btn btn-sm" style="background: #23546B; color: white; border: none; padding: 5px 15px; border-radius: 4px;">CRR</button>
                                        </form>
                                    </td> --}}

                                     @php
                                        $isLab = false;

                                        if ($Course) {
                                            $check = $Course->course->name ?? '';
                                            $isLab = strpos(strtolower($check), 'lab') !== false;
                                        }
                                    @endphp

                                    @if($isLab)
                                        <td style="text-align: center;">
                                            <form method="get" action="{{ route('marks.exportlab') }}"
                                                style="background-color: #00556c; padding:0; border-radius:20px; box-shadow:0; max-width:400px; width:80px; margin:0 auto;">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $Course->course_id }}">
                                                <input type="hidden" name="faculty_id" value="{{ $Course->faculty->user_id ?? '' }}">
                                                <button type="submit" style="color:#ffffff;" class="btn btn-sm">OBE LAB</button>
                                            </form>
                                        </td>
                                        <td style="text-align: center;">
                                            <form method="get" action="{{ route('marks.exportlab.word') }}"
                                                style="background-color: #00556c; padding:0; border-radius:20px; box-shadow:0; max-width:400px; width:80px; margin:0 auto;">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $Course->course_id }}">
                                                <input type="hidden" name="faculty_id" value="{{ $Course->faculty->user_id ?? '' }}">
                                                <button type="submit" style="color:#ffffff;" class="btn btn-sm">CRR</button>
                                            </form>
                                        </td>
                                    @else
                                        <td style="text-align: center;">
                                            <form method="get" action="{{ route('marks.export') }}"
                                                style="background-color: #00556c; padding:0; border-radius:20px; box-shadow:0; max-width:400px; width:80px; margin:0 auto;">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $Course->course_id }}">
                                                <input type="hidden" name="faculty_id" value="{{ $Course->faculty->user_id ?? '' }}">
                                                <button type="submit" style="color:#ffffff;" class="btn btn-sm">OBE</button>
                                            </form>
                                        </td>
                                        <td style="text-align: center;">
                                            <form method="get" action="{{ route('marks.export.word') }}"
                                                style="background-color: #00556c; padding:0; border-radius:20px; box-shadow:0; max-width:400px; width:80px; margin:0 auto;">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $Course->course_id }}">
                                                <input type="hidden" name="faculty_id" value="{{ $Course->faculty->user_id ?? '' }}">
                                                <button type="submit" style="color:#ffffff;" class="btn btn-sm">CRR</button>
                                            </form>
                                        </td>
                                    @endif
                                    <td>
                                        <a href="{{ route('courses.setCourseSession', $Course->course_id) }}" class="btn btn-sm" style="background: #23546B; color: white; border: none; padding: 5px 15px; border-radius: 4px;">View All</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-danger">No courses found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
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

@stack('scripts')

</body>
</html>





























