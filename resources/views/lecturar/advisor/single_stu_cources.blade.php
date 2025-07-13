{{-- single_stu_cources.blade.php --}}

@extends('lecturar.dashboard')

@section('title', 'Add Course')
<style>
   .topbar{
    display: flex;
    align-items: center;
    justify-content:space-between;
    margin-bottom: 10px;
   } 
   .innerbuttons{
    display: flex;
    align-items: center;
    justify-content:end;
   } 
   .faculty-table {
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

    /* Background blur overlay */
    #loader-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100vw;
        height: 100vh;
        backdrop-filter: blur(5px);
        background-color: rgba(0, 0, 0, 0.3);
        z-index: 9998;
    }

    /* Loader box */
    #loader {
        display: none;
        position: fixed;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background: #ffffff;
        padding: 30px 50px;
        border: 3px solid #3c9aa5;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 0 30px rgba(0,0,0,0.3);
        color: #23546b;
        font-weight: bold;
    }

    /* Spinner animation */
    .spinner {
        margin: 15px auto;
        width: 40px;
        height: 40px;
        border: 4px solid #3c9aa5;
        border-top: 4px solid #23546b;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
    

@section('content')

    
<!-- Loader Overlay + Box -->
    <div id="loader-overlay"></div>
    <div id="loader">
        <div class="spinner"></div>
        Updating status...
    </div>
    <h1></h1>
    <div id="defaultContent">
        <h1 class="mt-4 text-center">Welcome to the Cource Advisor Dashboard</h1>
        @if($CourseRegistration->isNotEmpty())
            @php
                $registration = $CourseRegistration->first(); 
            @endphp
            <h3>Student Name: {{$registration->student->name}}</h3>
            <h3>Roll No: {{$registration->studentDetails->roll_number}}</h3>
        @else
            <p>No course registrations available.</p>
        @endif
        
       
       
        <table class="faculty-table">
            <thead>
                <tr>
                    <th>#</th>
                    {{-- <th>Student Name</th> --}}
                    {{-- <th>Roll number</th> --}}
                    <th>course Title</th>
                    <th>Class</th>
                    <th>Faculty Name</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($CourseRegistration as $index => $registration)
                    <tr>
                        <?php
                            // $studentData = \App\Models\Stu::where('user_id', $registration->student->id)->first();
                        ?>
                        <td>{{ $index + 1 }}</td> <!-- Serial Number -->
                        {{-- <td>{{ $registration->student->name }}</td> <!-- Course Title --> --}}
                        {{-- <td>{{ $registration->studentDetails->roll_number }}</td> <!-- Roll number --> --}}
                        <td>{{ $registration->course->name }} ({{ $registration->course->code }})</td> <!-- Course Title -->
                        <td>{{ $registration->courseAllocation->batch }} - {{ $registration->courseAllocation->section }}</td> <!-- Class -->
                        <td>{{ optional($registration->courseAllocation->faculty->user)->name ?? 'N/A' }}</td> <!-- Faculty Name -->
                        {{-- <td>{{ ucfirst($registration->id) }}</td>  --}}
                        {{-- <td>{{ ucfirst($registration->status) }}</td> <!-- Status --> --}}
                        {{-- <td id="status-{{ $registration->id }}">{{ ucfirst($registration->status) }}</td> --}}
                        <td >
                            <select id="status-select-{{ $registration->id }}" onchange="updateStatus({{ $registration->id }})">
                                <option value="approved" {{ $registration->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $registration->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="pending" {{ $registration->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-danger">No Cources found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
    </div>

    <script>
        function updateStatus(id) {
            var status = $('#status-select-' + id).val();

            // Show loader and overlay
            $('#loader, #loader-overlay').show();

            $.ajax({
                url: '/update-status/' + id,
                type: 'POST',
                data: {
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // alert(response.message);
                    } else {
                        alert('Failed to update status');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                   // alert('An error occurred while updating the status.');
                },
                complete: function() {
                    // Hide loader and overlay
                    $('#loader, #loader-overlay').hide();
                }
            });
        }
        </script>
@endsection
