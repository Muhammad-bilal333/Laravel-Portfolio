@extends('lecturar.dashboard')

@section('title', 'Create Course')

@section('content')
<div class="container mt-4">
    <h2>Create Course</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('newcourse.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Course Name:</label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>

        <div class="form-group">
            <label for="code">Course Code:</label>
            <input type="text" class="form-control" name="code" id="code" required>
        </div>

        <div class="form-group">
            <label for="semester">Semester:</label>
            <select class="form-control" name="semester" id="semester" required>
                <option value="">-- Select Semester --</option>
                @for ($i = 1; $i <= 8; $i++)
                    <option value="{{ $i }}">Semester {{ $i }}</option> <!-- Store only number -->
                @endfor
            </select>
        </div>

        <div class="form-group">
            <label for="pre_req">Pre-Req:</label>
            <select class="form-control" name="pre_req" id="pre_req" required>
                <option value="">-- Choose Course --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                @endforeach
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
        <div class="form-group">
            <label for="code">Credit Hours:</label>
            <input type="text" class="form-control" name="Credit_Hours" id="Credit_Hours" required>
        </div>
        <div class="form-group">
            <label for="code">Status:</label>
            <input type="text" class="form-control" name="Status" id="Status" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Course</button>
    </form>
</div>
@endsection