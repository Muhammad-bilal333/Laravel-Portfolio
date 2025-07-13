@extends('lecturar.dashboard')
@section('content')
<div class="container d-flex justify-content-center">
    <div style="margin-top:20px; width: 100%; max-width: 900px; height: 700px; overflow-y: auto; padding: 20px; border: 1px solid #ccc; background-color: #fff; border-radius: 8px;">
        {{-- Course Title and Intro --}}
        @php
            $details = $courses_detail;
            $intro = $details->course_detail->first();
        @endphp
        <h2 class="mb-4">{{ $intro->title ?? 'Course Title' }}</h2>

        <h4>Course Introduction & Objectives:</h4>
        <p>{{ $intro->intro_objectives ?? 'No introduction available.' }}</p>

        {{-- Course Outcomes --}}
        <h4 class="mt-5">Course Outcomes:</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>CLO</th>
                    <th>Description</th>
                    <th>Bloom's Level</th>
                    <th>PLO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses_detail->course_outcome as $outcome)
                    <tr>
                        <td>{{ $outcome->clo }}</td>
                        <td>{{ $outcome->description }}</td>
                        <td>{{ $outcome->blooms_level }}</td>
                        <td>{{ $outcome->PLO }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Course Content --}}
        <h4 class="mt-5">Course Contents:</h4>
        @foreach($courses_detail->course_content as $content)
            <h5>{{ $content->heading_number }}. {{ $content->heading_title }}</h5>
            <ul>
                @foreach($courses_detail->course_content_point->where('course_contents_id', $content->id) as $point)
                    <li>{{ $point->description }}</li>
                @endforeach
            </ul>
        @endforeach

        {{-- Practical Outcomes --}}
        <h4 class="mt-5">Practical Outcomes:</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>CLO</th>
                    <th>Description</th>
                    <th>Bloom's Level</th>
                    <th>PLO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses_detail->practical_outcome as $practical)
                    <tr>
                        <td>{{ $practical->clo }}</td>
                        <td>{{ $practical->description }}</td>
                        <td>{{ $practical->blooms_level }}</td>
                        <td>{{ $practical->PLO }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
