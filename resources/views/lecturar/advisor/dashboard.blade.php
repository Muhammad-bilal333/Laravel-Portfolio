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
</style>

@section('content')
    <h1></h1>
    <div id="defaultContent">
        <h1 class="mt-4 text-center">Welcome to the Cource Advisor Dashboard</h1>
        
        <div class="topbar">
            <h3 class="topbar-heading">Student Registration Requests</h3>
            <div class="innerbuttons">
              @php
                $Course = $CourseRegistration->first();
            @endphp

            @if ($Course)
                <a href="{{ route('courses.plo_counseling', ['id' => $Course->student_id]) }}" class="btn btn-info">
                    PLO Counseling
                </a>
            @endif

            

                {{-- <form method="get" action="{{ route('marks.export') }}"
                    style="background-color: #00556c;  padding:0; border-radius:20px; box-shadow:0; max-width:400px;  width:80px; margin-top:0;">
                   @csrf
                   <input type="hidden" name="course_id" value="{{$Course->course_id}}">
                   <input type="hidden" name="faculty_id" value="{{ $Course->faculty->user_id ?? '' }}">
                   <button type="submit" style="color:#ffffff;" class="btn btn-sm">OBE</button>
                </form> --}}

            </div>
        </div>
       
        <table class="faculty-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Registration NO</th>
                    <th>Student Name</th>
                    <th>All Cources</th>
                </tr>
            </thead>

            <tbody>
                @forelse($CourseRegistration as $index => $registration)
                    <tr>
                        {{-- {{dd( $registration->studentDetails->roll_number);}} --}}
                        <?php
                            // $studentData = \App\Models\Stu::where('user_id', $registration->student->id)->first();
                        ?>
                        <td>{{ $index + 1 }}</td> <!-- Serial Number -->
                        {{-- <td>FUI/FURC/{{ $registration->courseAllocation->batch}}-BSET-{{ $registration->studentDetails->roll_number }}</td> <!-- Course Title --> --}}
                        <td>
                            FUI/FURC/{{ optional($registration->courseAllocation)->batch ?? '_' }}-BSET-{{ optional($registration->studentDetails)->roll_number ?? '_' }}
                        </td>
                        <td>{{ $registration->student->name }}</td>
                        {{-- <td>{{ optional($registration->courseAllocation->faculty->user)->name ?? 'N/A' }}</td> <!-- Faculty Name --> --}}
                        <td><a href="{{ route('courses.SingleStuCources', ['id' => $registration->student_id]) }}">All Cource Requests</a></td>
                        {{-- <td>{{ ucfirst($registration->status) }}</td> <!-- Status --> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-danger">No Cource Request Yet!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
    </div>
@endsection


