<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Faculty</title>
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
            margin-bottom: 18px;
            text-align: center;
            position: relative;
            padding-bottom: 0;
        }
        .form-title:after {
            display: none;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 0;
        }
        .form-col-half {
            flex: 1 1 0;
            min-width: 0;
        }
        .form-group {
            margin-bottom: 8px;
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
        .form-check {
            margin-bottom: 0;
        }
        .form-check input[type="checkbox"] {
            margin-right: 8px;
        }
        .form-check label {
            color: #555;
            font-weight: normal;
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
            margin-bottom: 10px;
        }
        .form-section-title {
            color: #23546B;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .form-section-title + .form-row,
        .form-section-title + .form-group,
        .form-section-title + .duties-row {
            margin-top: 8px;
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
        .duties-row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            margin-bottom: 0;
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

        /* Add these styles to your existing CSS */
        .is-invalid {
            border-color: #dc3545 !important;
            background-color: #fff8f8 !important;
        }
        
        .is-valid {
            border-color: #28a745 !important;
            background-color: #f8fff8 !important;
        }
        
        .text-danger {
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
            display: block;
        }
        
        input:focus, select:focus {
            box-shadow: 0 0 0 0.2rem rgba(60, 154, 165, 0.25) !important;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .validation-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .is-valid + .validation-icon {
            color: #28a745;
        }
        
        .is-invalid + .validation-icon {
            color: #dc3545;
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
            <div class="adjust" style="width:100%; max-width:600px; position:relative;">
                <a href="{{ route('admin.dashboard') }}" title="Back to Dashboard" style="position:absolute; top:16px; left:-220px; font-size:22px; color:#23546B; background:none; border:none; padding:0; cursor:pointer; z-index:2;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 20px 0; padding: 15px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <form id="facultyForm" method="POST" action="{{ route('register.faculty') }}">
                    <h2 class="form-title">Add New Faculty Member</h2>
                    @csrf
                    
                    <div class="form-section">
                        <div class="form-section-title">Personal Information</div>
                        <div class="form-row">
                            <div class="form-group form-col-half">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" placeholder="Enter faculty name" value="{{ old('name') }}">
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
                        <div class="form-section-title">Department Information</div>
                        <div class="form-row">
                            <div class="form-group form-col-half">
                                <label for="department">Department</label>
                                <select name="department" id="department" required>
                                    <option value="">Select Department</option>
                                    <option value="IT" {{ old('department') == 'IT' ? 'selected' : '' }}>Information Technology</option>
                                </select>
                                <p class="text-danger">{{ $errors->first('department') }}</p>
                            </div>
                            <div class="form-group form-col-half">
                                <label for="designation">Designation</label>
                                <select name="designation" id="designation" required>
                                    <option value="">Select Designation</option>
                                    @foreach($primaryRoles as $role)
                                        <option value="{{ $role->id }}" {{ old('designation') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-danger">{{ $errors->first('designation') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">Additional Duties</div>
                        <div class="duties-row">
                            @foreach($dutyRoles as $duty)
                                <div class="form-check">
                                    <input type="checkbox" name="duties[]" value="{{ $duty->id }}" id="duty{{ $duty->id }}" 
                                        {{ in_array($duty->id, old('duties', [])) ? 'checked' : '' }}>
                                    <label for="duty{{ $duty->id }}">{{ $duty->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-danger">{{ $errors->first('duties') }}</p>
                    </div>

                    <button type="submit" class="btn-submit">Add Faculty Member</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".mobilemenu").click(function() {
            $(".sidebar").toggleClass("active");
            $(this).toggleClass("fa-bars fa-times");
        });

        // Auto-dismiss alerts after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 3000);

        // Real-time validation for name
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

        // Form submission validation
        $('#facultyForm').on('submit', function(e) {
            let isValid = true;
            
            // Trigger validation for all fields
            $('#name, #email, #password').trigger('input');
            
            // Check if any field has errors
            if ($('.is-invalid').length > 0) {
                e.preventDefault();
                isValid = false;
            }

            // Validate required selects
            $('#department, #designation').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    $(this).siblings('.text-danger').text('This field is required');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                }
            });

            return isValid;
        });

        // Clear validation on focus
        $('input, select').on('focus', function() {
            $(this).removeClass('is-invalid is-valid');
            $(this).siblings('.text-danger').text('');
        });
    });
</script>
</body>
</html>





























