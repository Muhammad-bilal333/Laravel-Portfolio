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
</style>

@section('content')
    <h1></h1>
    <div id="defaultContent">
        <h1 class="mt-4 text-center">Welcome to the Cource Advisor Dashboard</h1>
        
        
       
        <table class="faculty-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Roll number</th>
                    {{-- <th>course Title</th> --}}
                    <th>Class</th>
                    {{-- <th>Faculty Name</th> --}}
                    <th>All Cources</th>
                </tr>
            </thead>

            <tbody>
                @forelse($CourseRegistration as $index => $registration)
                    <tr>
                        <?php
                            // $studentData = \App\Models\Stu::where('user_id', $registration->student->id)->first();
                        ?>
                        <td>{{ $index + 1 }}</td> <!-- Serial Number -->
                        
                        <td>{{ $registration->student->name }}</td> <!-- Course Title -->
                        <td>{{ $registration->studentDetails->roll_number }}</td> <!-- Roll number -->
                        {{-- <td>{{ $registration->course->name }} ({{ $registration->course->code }})</td> <!-- Course Title --> --}}
                        <td>{{ $registration->courseAllocation->batch }} - {{ $registration->courseAllocation->section }}</td> <!-- Class -->
                        {{-- <td>{{ optional($registration->courseAllocation->faculty->user)->name ?? 'N/A' }}</td> <!-- Faculty Name --> --}}
                        <td><a href="{{ route('courses.SingleStuCources', ['id' => $registration->student_id]) }}">All Cource Requests</a></td>
                        {{-- <td>{{ ucfirst($registration->status) }}</td> <!-- Status --> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-danger">No faculty found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
    </div>
@endsection
