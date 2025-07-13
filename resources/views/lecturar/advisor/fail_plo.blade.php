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
        <h1 class="mt-4 text-center">fail_plo</h1>
        {{ dd('ok') }}  
        {{-- @if(count($lowPLOStudents))
                <h4>Students with PLO < 50%</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Name</th>
                            <th>PLO 1</th>
                            <th>PLO 2</th>
                            <th>PLO 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowPLOStudents as $student)
                            <tr>
                                <td>{{ $student['roll_no'] }}</td>
                                <td>{{ $student['name'] }}</td>
                                <td>{{ $student['plo1'] }}%</td>
                                <td>{{ $student['plo2'] }}%</td>
                                <td>{{ $student['plo3'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-success">All students have PLOs above 50%.</p>
            @endif --}}
    </div>
@endsection
