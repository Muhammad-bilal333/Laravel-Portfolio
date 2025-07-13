{{-- AssignCourceAdvisor.blade.php --}}
@extends('lecturar.dashboard')

@section('title', 'View Courses')

@section('content')
    <div class="container mt-4">
        <h2>Assign Course Advisor To Batch</h2>

        @if(session('success'))
            <div  class="alert alert-success mt-4">{{ session('success') }}</div>
        @endif

        <form action="{{ route('Assign.advisor.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="faculty_id">Select Faculty:</label>
                <select class="form-control" name="faculty_id" id="faculty_id" required>
                    <option value="">-- Choose Faculty --</option>
                    @foreach($faculties as $faculty)
                        <option value="{{ $faculty->user->id }}">{{ $faculty->user->name }} - {{ $faculty->designation }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="batch">Batch:</label>
                <select id="batch" name="batch" required>
                    <option value="" disabled selected>Select Batch</option>
                    @for ($year = 19; $year <= 25; $year++)
                        <option value="FA-{{ $year }}">FA-{{ $year }}</option>
                        <option value="SP-{{ $year }}">SP-{{ $year }}</option>
                    @endfor
                </select>
            </div>
            
            <div class="form-group">
                <label for="section">Section:</label>
                <select class="form-control" name="section" id="section" required>
                    <option value="">-- Select Section --</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Assign Batch</button>
        </form>
    </div>
@endsection






















