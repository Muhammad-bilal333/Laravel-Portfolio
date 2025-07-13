<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/FUSSTLogo.jpg') }}">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #23546B;
            --secondary-color: #3C9AA5;
            --background-color: #E2ECF2;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(to bottom, #23546B, #3C9AA5);
        }

        .logo {
            max-width: 300px;
            border-radius: 30px;
            mix-blend-mode: color-burn;
        }

        .logo-heading {
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
            margin-left: 1rem;
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

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(to bottom, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 2rem 1rem;
            height: 100vh;
            box-shadow: var(--card-shadow);
        }

        .btn-sidebar {
            width: 100%;
            text-align: left;
            background: transparent;
            border: none;
            color: white;
            padding: 0.8rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-sidebar:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        /* Main Content Styles */
        .main-content {
            padding: 2rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .card-title {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--secondary-color);
        }

        /* Table Styles */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 1rem;
        }

        .custom-table th {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            font-weight: 500;
        }

        .custom-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .custom-table tr:hover {
            background-color: #f8f9fa;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-approved {
            background-color: #28a745;
            color: white;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-rejected {
            background-color: #dc3545;
            color: white;
        }

        /* Course Registration Form */
        .course-registration {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: var(--card-shadow);
        }

        .course-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                height: auto;
                padding: 1rem;
            }

            .main-content {
                padding: 1rem;
            }

            .logo {
                max-width: 200px;
                border-radius: 30px;
                mix-blend-mode: color-burn;
            }

            .logo-heading {
                font-size: 1.2rem;
            }

            .dashboard-card {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="container-fluid p-0">
        <nav class="col-md-12 col-lg-12 navbar">
            <div class="container-fluid">
                <img src="{{ asset('img/logo_wn.png') }}" alt="FUI Logo" class="logo img-fluid">
                <div class="icon-container">
                    <a href="{{ route('student.dashboard') }}" title="Home">
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
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <button class="btn btn-sidebar font-weight-bold" data-toggle="collapse" data-target="#facultyMenu">
                    <i class="fas fa-graduation-cap mr-2"></i>Academic Details
                </button>
                <div id="facultyMenu" class="collapse">
                    <a href="#" class="btn btn-sidebar">
                        <i class="fas fa-chart-bar mr-2"></i>Results
                    </a>
                </div>

                <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                
                <button class="btn btn-sidebar font-weight-bold" onclick="document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i>Sign out
                </button>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Course Recommendations Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Course Recommendations</h5>
                    </div>
                    <div class="card-body">
                        @forelse($courseRecommendations as $registration)
                            <div class="recommendation-item border-bottom p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>{{ $registration->course->name }} ({{ $registration->course->code }})</h6>
                                        <p class="text-muted mb-0">Recommended to improve PLO performance</p>
                                    </div>
                                    <div>
                                        @if($registration->status === 'pending')
                                            <button class="btn btn-success btn-sm accept-recommendation" 
                                                data-registration-id="{{ $registration->id }}">
                                                Accept
                                            </button>
                                            <button class="btn btn-danger btn-sm reject-recommendation"
                                                data-registration-id="{{ $registration->id }}">
                                                Reject
                                            </button>
                                        @else
                                            <span class="badge badge-{{ $registration->status === 'approved' ? 'success' : 'danger' }}">
                                                {{ ucfirst($registration->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No course recommendations at this time.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Registered Courses Section -->
                <div class="dashboard-card">
                    <h2 class="card-title">
                        <i class="fas fa-book mr-2"></i>Registered Courses
                    </h2>
                    @if($approvedRegistrations->isNotEmpty())
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Course Title</th>
                                        <th>Class</th>
                                        <th>Faculty</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedRegistrations as $index => $registration)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $registration->course->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $registration->course->code }}</small>
                                            </td>
                                            <td>
                                                @if($registration->courseAllocation)
                                                    {{ $registration->courseAllocation->batch }} - {{ $registration->courseAllocation->section }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($registration->courseAllocation && $registration->courseAllocation->faculty && $registration->courseAllocation->faculty->user)
                                                    {{ $registration->courseAllocation->faculty->user->name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span class="status-badge status-approved">
                                                    <i class="fas fa-check-circle mr-1"></i>Approved
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No courses registered yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Pending Courses Section -->
                @if($pendingRegistrations->isNotEmpty())
                    <div class="dashboard-card">
                        <h2 class="card-title">
                            <i class="fas fa-clock mr-2"></i>Pending Registrations
                        </h2>
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Course Title</th>
                                        <th>Class</th>
                                        <th>Faculty</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRegistrations as $index => $registration)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $registration->course->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $registration->course->code }}</small>
                                            </td>
                                            <td>
                                                @if($registration->courseAllocation)
                                                    {{ $registration->courseAllocation->batch }} - {{ $registration->courseAllocation->section }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($registration->courseAllocation && $registration->courseAllocation->faculty && $registration->courseAllocation->faculty->user)
                                                    {{ $registration->courseAllocation->faculty->user->name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span class="status-badge status-pending">
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Available Courses Section -->
                @if($courses->isNotEmpty())
                    <div class="dashboard-card course-registration">
                        <h2 class="card-title">
                            <i class="fas fa-plus-circle mr-2"></i>New Courses Available for Registration
                        </h2>
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle mr-2"></i>
                            The following courses are newly available for your batch and section. You can register for these courses if you haven't already.
                        </div>
                        <form action="{{ route('courses.register') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="custom-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Course Name</th>
                                            <th>Teacher Name</th>
                                            <th>Class</th>
                                            <th>Register</th>
                                        </tr>
                                    </thead>
                                    <tbody id="course-list">
                                        @foreach ($courses as $index => $course)
                                            <tr data-semester="{{ $course->semester }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $course->course->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $course->course->code }}</small>
                                                </td>
                                                <td>{{ $course->faculty->user->name }}</td>
                                                <td>
                                                    @if(empty($course->section))
                                                        -
                                                    @else
                                                        {{ $course->batch }} - {{ $course->section }}
                                                    @endif
                                                </td>
                                                <input type="hidden" name="teacher_ids[]" value="{{ $course->faculty->user_id }}">
                                                <input type="hidden" name="course_ids[]" value="{{ $course->course_id }}">
                                                <input type="hidden" name="course_allocation_ids[]" value="{{ $course->id }}">
                                                <input type="hidden" name="student_id" value="{{ $coreuser->id }}">
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $course->id }}" class="course-checkbox">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save mr-2"></i>Register Selected Courses
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Auto-dismiss alerts after 3 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 3000);

            // Handle course selection
            let checkboxes = document.querySelectorAll('.course-checkbox');
            let selectedCourses = document.getElementById('selected-courses');

            function updateCounts() {
                let selected = 0;
                checkboxes.forEach(chk => {
                    if (chk.checked) selected++;
                });
                if (selectedCourses) {
                    selectedCourses.textContent = selected;
                }
            }

            checkboxes.forEach(chk => chk.addEventListener('change', updateCounts));
            updateCounts();

            $('.accept-recommendation, .reject-recommendation').click(function() {
                const registrationId = $(this).data('registration-id');
                const status = $(this).hasClass('accept-recommendation') ? 'approved' : 'rejected';
                
                $.post(`/update-status/${registrationId}`, {
                    status: status,
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error updating status: ' + response.message);
                    }
                });
            });
        });
    </script>
</body>
</html>





























