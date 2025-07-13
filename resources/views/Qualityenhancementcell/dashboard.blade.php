<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QEC</title>
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
        }

        .form-control::placeholder {
            color: white !important;
            opacity: 0.7;
        }
        .form-select::placeholder {
            color: white !important;
            opacity: 0.7;
        }
        .form-control {
            color: white !important;
        }
        .form-select {
            color: white !important;
        }
        .form-control:focus {
            color: white !important;
        }
        .form-select:focus {
            color: white !important;
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

        /* New styles for CRR cards */
        .crr-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .crr-card:hover {
            transform: translateY(-5px);
        }
        .crr-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        .crr-status.pending {
            background-color: #ffc107;
            color: #000;
        }
        .crr-status.reviewed {
            background-color: #28a745;
            color: #fff;
        }
        .crr-status.needs-attention {
            background-color: #dc3545;
            color: #fff;
        }
        .crr-details {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .crr-actions {
            margin-top: 15px;
        }
        .search-box {
            margin-bottom: 20px;
        }
        .filter-section {
            margin-bottom: 20px;
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

    <div class="row m-0" >
        <!-- Sidebar Section -->
        <div class="col-md-3 col-lg-2 sidebar">
            {{-- <a href="{{ route('qec.failed-plos') }}" class="btn btn-sidebar font-weight-bold mb-2" style="color: white; font-size: 1.1rem;">CRR</a> --}}
            <form id="logout-form" method="POST" action="{{ route('qec.logout') }}" style="display: none;">@csrf</form>
            <button class="btn btn-sidebar font-weight-bold" style="color: white; width: 100%;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</button>
        </div>

        <!-- Main Content Section -->
        <div class="col-md-9 col-lg-10 mainbar">
            <div class="container mt-4">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="alert alert-info text-center" style="font-size: 1.5rem; background: linear-gradient(to right, #3C9AA5, #23546B); color: white; border-radius: 12px; border: none;">
                            <strong>Welcome to the Quality Enhancement Cell Dashboard!</strong>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- Search and Filter Section -->
                    <div class="col-md-6">
                        <div class="search-box">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search CRR sheets...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="filter-section">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending review">Pending Review</option>
                                <option value="reviewed">Reviewed</option>
                                <option value="needs attention">Needs Attention</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- CRR Sheets Grid -->
                    <div class="col-md-12" id="crrGrid">
                        <!-- CRR cards will be dynamically loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit CRR Modal -->
<div class="modal fade" id="editCRRModal" tabindex="-1" role="dialog" aria-labelledby="editCRRModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCRRModalLabel">Edit CRR Sheet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editCRRForm">
                    <input type="hidden" id="crrId">
                    <div class="form-group">
                        <label for="crrStatus">Status</label>
                        <select class="form-control" id="crrStatus" name="status">
                            <option value="pending review">Pending Review</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="needs attention">Needs Attention</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="crrComments">QEC Comments</label>
                        <textarea class="form-control" id="crrComments" name="qec_comments" rows="4"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCRRChanges">Save changes</button>
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

        // Function to load CRR data
        function loadCRRData() {
            $.ajax({
                url: '/qec/fetch-crr-data',
                method: 'GET',
                success: function(response) {
                    displayCRRCards(response.crrs);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching CRR data:', error);
                }
            });
        }

        // Function to display CRR cards
        function displayCRRCards(crrs) {
            const grid = $('#crrGrid');
            grid.empty();

            crrs.forEach(crr => {
                const card = `
                    <div class="col-md-6 col-lg-4">
                        <div class="card crr-card shadow-sm">
                            <div class="card-body">
                                <span class="crr-status ${crr.status.replace(' ', '-')}">${crr.status}</span>
                                <h5 class="card-title">${crr.course_name}</h5>
                                <p class="card-subtitle mb-2 text-muted">${crr.course_code}</p>
                                <div class="crr-details">
                                    <p><i class="fas fa-user"></i> Lecturer: ${crr.lecturer}</p>
                                    <p><i class="fas fa-users"></i> Total Students: ${crr.total_students}</p>
                                    <p><i class="fas fa-exclamation-triangle"></i> Failed PLOs: ${crr.failed_plos}</p>
                                    <p><i class="fas fa-clock"></i> Last Updated: ${crr.last_updated}</p>
                                </div>
                                <div class="crr-actions">
                                    <button class="btn btn-primary btn-sm" onclick="viewCRR(${crr.id})">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="editCRR(${crr.id})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                grid.append(card);
            });
        }

        // Search functionality
        $('#searchInput').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.crr-card').each(function() {
                const cardText = $(this).text().toLowerCase();
                $(this).closest('.col-md-6').toggle(cardText.includes(searchTerm));
            });
        });

        // Status filter functionality
        $('#statusFilter').on('change', function() {
            const status = $(this).val();
            if (status) {
                $('.crr-card').each(function() {
                    const cardStatus = $(this).find('.crr-status').text().toLowerCase();
                    $(this).closest('.col-md-6').toggle(cardStatus === status);
                });
            } else {
                $('.col-md-6').show();
            }
        });

        // Edit CRR functionality
        window.editCRR = function(crrId) {
            $.ajax({
                url: `/qec/view-crr/${crrId}`,
                method: 'GET',
                success: function(response) {
                    $('#crrId').val(crrId);
                    $('#crrStatus').val(response.status);
                    $('#crrComments').val(response.qec_comments);
                    $('#editCRRModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching CRR details:', error);
                }
            });
        };

        // Save CRR changes
        $('#saveCRRChanges').on('click', function() {
            const crrId = $('#crrId').val();
            const data = {
                status: $('#crrStatus').val(),
                qec_comments: $('#crrComments').val()
            };

            $.ajax({
                url: `/qec/update-crr/${crrId}`,
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#editCRRModal').modal('hide');
                    loadCRRData(); // Reload the CRR data
                    alert('CRR updated successfully!');
                },
                error: function(xhr, status, error) {
                    console.error('Error updating CRR:', error);
                    alert('Failed to update CRR. Please try again.');
                }
            });
        });

        // View CRR functionality
        window.viewCRR = function(crrId) {
            window.location.href = `/qec/view-crr/${crrId}`;
        };

        // Initial load
        loadCRRData();
    });
</script>

<div class="d-flex align-items-center">
    <div class="dropdown">
        <button class="btn btn-link text-white dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle me-2"></i>
            {{ Auth::user()->name }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li>
                <form method="POST" action="{{ route('qec.logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
</body>
</html>





























