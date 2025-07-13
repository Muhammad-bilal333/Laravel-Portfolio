<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
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
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            margin-top: 30px;
        }
        .form-title {
            color: #23546B;
            font-size: 30px;
            font-weight: 600;
            margin-bottom: 12px;
            text-align: center;
            position: relative;
            padding-bottom: 0;
        }
        .form-title:after {
            display: none;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #23546B;
            font-weight: 500;
            font-size: 14px;
        }
        input[type="text"], input[type="email"], 
        input[type="password"], select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e1e1e1;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        input[type="text"]:focus, input[type="email"]:focus, 
        input[type="password"]:focus, select:focus, textarea:focus {
            border-color: #3C9AA5;
            box-shadow: 0 0 0 2px rgba(60, 154, 165, 0.1);
            outline: none;
            background-color: #fff;
        }
        .btn-submit {
            background: linear-gradient(to right, #3C9AA5, #23546B);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            background: linear-gradient(to right, #23546B, #3C9AA5);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .text-danger {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }
        .form-section {
            background: #f8f9fa;
            padding: 10px 12px 2px 12px;
            border-radius: 7px;
            margin-bottom: 2px;
        }
        .form-section-title {
            color: #23546B;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .form-section-title + .form-row,
        .form-section-title + .form-group {
            margin-top: 8px;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .form-col-half {
            flex: 1 1 0;
            min-width: 0;
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
                <a href="{{ route('admin.dashboard') }}" title="Back to Dashboard" style="position:absolute; top:16px; left:-220px; font-size:22px; color:#23546B; background:none; border:none; padding:0; cursor:pointer; z-index:2;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <form id="facultyForm" method="POST" action="{{ route('register.student') }}">
                    <h2 class="form-title">Add New Student</h2>
                    @csrf
                    <div class="form-section">
                        <div class="form-section-title">Personal Information</div>
                        <div class="form-row">
                            <div class="form-group form-col-half">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" placeholder="Enter student name" value="{{ old('name') }}">
                                <p class="text-danger">{{ $errors->first('name') }}</p>
                            </div>
                            <div class="form-group form-col-half">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" placeholder="Enter email address" required value="{{ old('email') }}">
                                <p class="text-danger">{{ $errors->first('email') }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter password" required>
                            <div class="password-requirements mt-2">
                                <p class="mb-1" style="font-size: 12px; color: #666;">Password must contain:</p>
                                <ul class="pl-3" style="font-size: 12px; color: #666;">
                                    <li id="length" class="text-danger"><i class="fas fa-times"></i> At least 8 characters</li>
                                    <li id="lowercase" class="text-danger"><i class="fas fa-times"></i> One lowercase letter</li>
                                    <li id="uppercase" class="text-danger"><i class="fas fa-times"></i> One uppercase letter</li>
                                    <li id="number" class="text-danger"><i class="fas fa-times"></i> One number</li>
                                    <li id="special" class="text-danger"><i class="fas fa-times"></i> One special character (!@#$%^&*)</li>
                                </ul>
                            </div>
                            <p class="text-danger">{{ $errors->first('password') }}</p>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">Academic Information</div>
                        <div class="form-row">
                            <div class="form-group form-col-half">
                                <label for="reg_no">Registration Number</label>
                                <input type="text" id="reg_no" name="reg_no" placeholder="Enter registration number" value="{{ old('reg_no') }}">
                                <p class="text-danger">{{ $errors->first('reg_no') }}</p>
                            </div>
                            <div class="form-group form-col-half">
                                <label for="department">Department</label>
                                <select name="department" id="department" required>
                                    <option value="">Select Department</option>
                                    <option value="IT" {{ old('department') == 'IT' ? 'selected' : '' }}>Information Technology</option>
                                </select>
                                <p class="text-danger">{{ $errors->first('department') }}</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group form-col-half">
                                <label for="section">Section</label>
                                <select id="section" name="section">
                                    <option value="">Select Section</option>
                                    <option value="A" {{ old('section') == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('section') == 'B' ? 'selected' : '' }}>B</option>
                                </select>
                                <p class="text-danger">{{ $errors->first('section') }}</p>
                            </div>
                            <div class="form-group form-col-half">
                                <label for="batch">Batch</label>
                                <select id="batch" name="batch" required>
                                    <option value="">Select Batch</option>
                                    <option value="FA-21" {{ old('batch') == 'FA-21' ? 'selected' : '' }}>FA-21</option>
                                    <option value="SP-21" {{ old('batch') == 'SP-21' ? 'selected' : '' }}>SP-21</option>
                                    <option value="FA-22" {{ old('batch') == 'FA-22' ? 'selected' : '' }}>FA-22</option>
                                    <option value="SP-22" {{ old('batch') == 'SP-22' ? 'selected' : '' }}>SP-22</option>
                                    <option value="FA-23" {{ old('batch') == 'FA-23' ? 'selected' : '' }}>FA-23</option>
                                    <option value="SP-23" {{ old('batch') == 'SP-23' ? 'selected' : '' }}>SP-23</option>
                                    <option value="FA-24" {{ old('batch') == 'FA-24' ? 'selected' : '' }}>FA-24</option>
                                    <option value="SP-24" {{ old('batch') == 'SP-24' ? 'selected' : '' }}>SP-24</option>
                                    <option value="FA-25" {{ old('batch') == 'FA-25' ? 'selected' : '' }}>FA-25</option>
                                    <option value="SP-25" {{ old('batch') == 'SP-25' ? 'selected' : '' }}>SP-25</option>
                                </select>
                                <p class="text-danger">{{ $errors->first('batch') }}</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Add Student</button>
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

        // Enhanced password validation with real-time feedback
        $('#password').on('input', function() {
            const password = $(this).val();
            const passwordError = $(this).siblings('.text-danger');
            
            // Reset all requirements to invalid state
            $('.password-requirements li').removeClass('text-success').addClass('text-danger')
                .find('i').removeClass('fa-check').addClass('fa-times');
            
            if (!password) {
                passwordError.text('Password is required');
                $(this).addClass('is-invalid');
                return;
            }

            // Check each requirement
            if (password.length >= 8) {
                $('#length').removeClass('text-danger').addClass('text-success')
                    .find('i').removeClass('fa-times').addClass('fa-check');
            }
            
            if (/(?=.*[a-z])/.test(password)) {
                $('#lowercase').removeClass('text-danger').addClass('text-success')
                    .find('i').removeClass('fa-times').addClass('fa-check');
            }
            
            if (/(?=.*[A-Z])/.test(password)) {
                $('#uppercase').removeClass('text-danger').addClass('text-success')
                    .find('i').removeClass('fa-times').addClass('fa-check');
            }
            
            if (/(?=.*\d)/.test(password)) {
                $('#number').removeClass('text-danger').addClass('text-success')
                    .find('i').removeClass('fa-times').addClass('fa-check');
            }
            
            if (/(?=.*[!@#$%^&*])/.test(password)) {
                $('#special').removeClass('text-danger').addClass('text-success')
                    .find('i').removeClass('fa-times').addClass('fa-check');
            }

            // Check if all requirements are met
            if (password.length >= 8 && 
                /(?=.*[a-z])/.test(password) && 
                /(?=.*[A-Z])/.test(password) && 
                /(?=.*\d)/.test(password) && 
                /(?=.*[!@#$%^&*])/.test(password)) {
                passwordError.text('');
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).addClass('is-invalid');
            }
        });

        // Enhanced email validation
        $('#email').on('input', function() {
            const email = $(this).val();
            const emailError = $(this).siblings('.text-danger');
            
            if (!email) {
                emailError.text('Email is required');
                $(this).addClass('is-invalid');
                return;
            }

            // Professional email validation
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email)) {
                emailError.text('Please enter a valid professional email address');
                $(this).addClass('is-invalid');
                return;
            }

            // Check if email already exists
            $.ajax({
                url: '{{ route("check.email") }}',
                method: 'POST',
                data: {
                    email: email,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.exists) {
                        emailError.text('This email is already registered');
                        $('#email').addClass('is-invalid');
                    } else {
                        emailError.text('');
                        $('#email').removeClass('is-invalid').addClass('is-valid');
                    }
                }
            });
        });

        // Enhanced name validation
        $('#name').on('input', function() {
            const name = $(this).val();
            const nameError = $(this).siblings('.text-danger');
            
            if (!name) {
                nameError.text('Name is required');
                $(this).addClass('is-invalid');
                return;
            }

            if (!/^[a-zA-Z\s]*$/.test(name)) {
                nameError.text('Name should only contain letters and spaces');
                $(this).addClass('is-invalid');
                return;
            }

            if (name.length < 2) {
                nameError.text('Name must be at least 2 characters long');
                $(this).addClass('is-invalid');
                return;
            }

            nameError.text('');
            $(this).removeClass('is-invalid').addClass('is-valid');
        });
    });
</script>
</body>
</html>





























