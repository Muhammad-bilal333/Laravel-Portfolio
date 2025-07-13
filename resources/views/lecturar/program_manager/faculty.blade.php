@extends('lecturar.dashboard')

@section('title', 'View Courses')

<style>
    .heading{
        margin:40px;
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
    <h2 class="heading" >Available Faculty</h2>
    <table class="faculty-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Duties</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facultylist as $faculty)
            <tr>
                <td>{{ $faculty->user->name ?? 'N/A' }}</td> 
                <td>{{ $faculty->user->email ?? 'N/A' }}</td> 
                <td>{{ $faculty->designation }}</td>
                <td>{{ $faculty->department }}</td>
                @php
                    // Ensure duties are properly decoded and always return an array
                    $dutyIds = is_array($faculty->duties) ? $faculty->duties : json_decode($faculty->duties ?? '[]', true) ?? [];
    
                    // Static mapping of duty IDs to names
                    $dutyMap = [
                        9 => 'HOD',
                        10 => 'Program Manager',
                        11 => 'Course Advisor'
                    ];
    
                    // Convert duty IDs to role names
                    $dutyNames = array_map(fn($id) => $dutyMap[$id] ?? 'None', $dutyIds);
                @endphp
                <td>{{ !empty($dutyNames) ? implode(', ', $dutyNames) : 'None' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
@endsection
