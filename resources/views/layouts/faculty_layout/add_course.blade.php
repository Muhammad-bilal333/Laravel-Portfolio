@extends('layouts.faculty_layout')

@section('title', 'Add Course')

@section('content')
    <h1>Add a New Course</h1>
    <form method="POST" action="{{ route('faculty.store_course') }}">
        @csrf
        <input type="text" name="course_name" placeholder="Enter Course Name" required>
        <input type="submit" value="Add Course">
    </form>
@endsection
