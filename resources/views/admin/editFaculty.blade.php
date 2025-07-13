<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Faculty</title>
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
        .text-center{
            margin-bottom: 18px;
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
                font-size: 1rem;
            }
            .main-div{
                padding-right: 10px;
                padding-left: 10px;
            }
            input[type="text"], input[type="email"], 
            input[type="password"],  select, textarea {
                width: 100%;
                padding: 10px;
                margin-bottom: 5px;
                border-radius: 5px;
                border: 1px solid #ddd;
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
        <div class="col-md-9 col-lg-10 d-flex justify-content-center align-items-start main-div">
            <div style="width:100%; max-width:600px; position:relative;">
                <a href="{{ route('faculty.list') }}" title="Back to Faculty List" style="position:absolute; top:16px; left:-220px; font-size:22px; color:#23546B; background:none; border:none; padding:0; cursor:pointer; z-index:2;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <form method="POST" action="{{ route('update.faculty', $faculty->id) }}">
                    @csrf
                    @method('POST')
                    
                    <h2 class="text-center">Edit faculty</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $faculty->user->name) }}" required>
                            <p class="text-danger">{{ $errors->first('name') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $faculty->user->email) }}" required>
                            <p class="text-danger">{{ $errors->first('email') }}</p>
                        </div>
                    </div>
            
                    <div class="row">
                        <div class="col-md-12">
                            <label for="department">Department:</label>
                            <select id="department" name="department" required>
                                <option value="Information Technology" {{ $faculty->department == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                            </select>
                            <p class="text-danger">{{ $errors->first('department') }}</p>
                        </div>
            
                        <div class="col-md-6">
                            <label>Designation:</label>
                            <div>
                                <input type="radio" id="lab_engineer" name="designation" value="Lab Engineer" {{ $faculty->designation == 'Lab Engineer' ? 'checked' : '' }}>
                                <label for="lab_engineer">Lab Engineer</label>
                            </div>
                            <div>
                                <input type="radio" id="lecturer" name="designation" value="Lecturer" {{ $faculty->designation == 'Lecturer' ? 'checked' : '' }}>
                                <label for="lecturer">Lecturer</label>
                            </div>
                            <div>
                                <input type="radio" id="assistant_professor" name="designation" value="Assistant Professor" {{ $faculty->designation == 'Assistant Professor' ? 'checked' : '' }}>
                                <label for="assistant_professor">Assistant Professor</label>
                            </div>
                            <div>
                                <input type="radio" id="associate_professor" name="designation" value="Associate Professor" {{ $faculty->designation == 'Associate Professor' ? 'checked' : '' }}>
                                <label for="associate_professor">Associate Professor</label>
                            </div>
                            <div>
                                <input type="radio" id="professor" name="designation" value="Professor" {{ $faculty->designation == 'Professor' ? 'checked' : '' }}>
                                <label for="professor">Professor</label>
                            </div>
                            <p class="text-danger">{{ $errors->first('designation') }}</p>
                        </div>
                    </div>
            
                    <label>Departmental Duties:</label>
                    @php
                            // Map numeric values to duty names
                            $dutyMap = [
                                9 => 'HOD',
                                10 => 'Program Manager',
                                11 => 'Course Advisor'
                            ];

                            // Decode the duties
                            $selectedDuties = json_decode($faculty->duties, true) ?? [];
                    @endphp
                    {{-- {{dd($selectedDuties)}} --}}
                    <div>
                        <input type="checkbox" id="hod" name="duties[]" value="9" 
                            {{ in_array(9, $selectedDuties) ? 'checked' : '' }}>
                        <label for="hod">HOD</label>
                    </div>
                    <div>
                        <input type="checkbox" id="program_manager" name="duties[]" value="10" 
                            {{ in_array(10, $selectedDuties) ? 'checked' : '' }}>
                        <label for="program_manager">Program Manager</label>
                    </div>
                    <div>
                        <input type="checkbox" id="course_advisor" name="duties[]" value="11" 
                            {{ in_array(11, $selectedDuties) ? 'checked' : '' }}>
                        <label for="course_advisor">Course Advisor</label>
                    </div>
                    <div>
                        <input type="checkbox" id="none" name="duties[]" value="" 
                            {{ empty($selectedDuties) ? 'checked' : '' }}>
                        <label for="none">None</label>
                    </div>
                    <p class="text-danger">{{ $errors->first('duties') }}</p>


                    {{-- <label>Departmental Duties:</label>
                    @php
                        $selectedDuties = json_decode($faculty->duties, true) ?? [];
                    @endphp
                    <div>
                        <input type="checkbox" id="hod" name="duties[]" value="HOD" {{ in_array('HOD', $selectedDuties) ? 'checked' : '' }}>
                        <label for="hod">HOD</label>
                    </div>
                    <div>
                        <input type="checkbox" id="program_manager" name="duties[]" value="Program Manager" {{ in_array('Program Manager', $selectedDuties) ? 'checked' : '' }}>
                        <label for="program_manager">Program Manager</label>
                    </div>
                    <div>
                        <input type="checkbox" id="course_advisor" name="duties[]" value="Course Advisor" {{ in_array('Course Advisor', $selectedDuties) ? 'checked' : '' }}>
                        <label for="course_advisor">Course Advisor</label>
                    </div>
                    <div>
                        <input type="checkbox" id="none" name="duties[]" value="None" {{ in_array('None', $selectedDuties) ? 'checked' : '' }}>
                        <label for="none">None</label>
                    </div>
                    <p class="text-danger">{{ $errors->first('duties') }}</p> --}}
                    
                    <input type="submit" value="Update">
                </form>
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





























