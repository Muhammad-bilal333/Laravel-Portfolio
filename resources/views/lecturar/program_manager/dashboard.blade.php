@extends('lecturar.dashboard')

@section('title', 'Add Course')
<style>
   .topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap;
    padding: 10px 20px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #ddd;
}

.topbar-heading {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    white-space: nowrap;
}

.innerbuttons {
    display: flex;
    gap: 10px;
}

.innerbuttons .btn {
    white-space: nowrap;
    padding: 6px 12px;
    font-size: 0.95rem;
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
        <h1 class="mt-4 text-center">Welcome to the Program manager Dashboard</h1>
        
        
        <div class="topbar">
            <h3 class="topbar-heading">Current Courses Allocated</h3>
            <div class="innerbuttons">
                <a href="{{ route('Assign.advisor') }}" class="btn btn-info">Assign Cource Advisor</a>
                <a href="{{ route('course.list') }}" class="btn btn-info">View Courses</a>
                <a href="{{ route('facultypro.list') }}" class="btn btn-info">Manage Faculty</a>
                <a href="{{ route('course.allocate') }}" class="btn btn-info">Allocate Courses</a>
            </div>
        </div>
        <table class="faculty-table">
            <thead>
                <tr>
                    <th>Course No</th>
                    <th>Course Title</th>
                    <th>Batch</th>
                    <th>Semester</th>
                    <th>Section</th>
                    <th>Teacher</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ProgramCourseAllocations as $Course)
                @php
                    $faculty = $Course->faculty ?? null;
                    $teacher = $faculty ? \App\Models\User::find($faculty->user_id) : null;
                @endphp
                {{-- {{dd( $Course->course->id)}} --}}
                {{-- {{ dd($teacher->name); }} --}}
                    <tr>
                        <td>{{ $Course->course->code}}</td>
                        <td>{{ $Course->course->name}}</td>
                        <td>{{ $Course->batch }}</td> 
                        <td>{{ $Course->course->semester }}</td> 
                        <td>{{ $Course->section }}</td>
                        <td>{{ $teacher ? $teacher->name : 'Faculty Missing' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-danger">No faculty found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
    </div>
@endsection
