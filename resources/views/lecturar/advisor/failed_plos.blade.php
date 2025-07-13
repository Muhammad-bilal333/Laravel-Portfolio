@extends('lecturar.dashboard')

@section('title', 'Failed PLOs')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Failed PLOs for {{ $course->name }} ({{ $course->code }})</h3>
        </div>
        <div class="card-body">
            @if(count($failedStudents) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Failed PLOs</th>
                                <th>Recommended Courses</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($failedStudents as $studentId => $student)
                                <tr>
                                    <td>{{ $student['name'] }}</td>
                                    <td>
                                        <ul class="list-unstyled">
                                            @foreach($student['failedPlos'] as $plo => $score)
                                                <li>{{ $plo }}: {{ $score }}%</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        @if(count($student['recommendedCourses']) > 0)
                                            <select class="form-control recommended-course" 
                                                    data-student-id="{{ $studentId }}">
                                                <option value="">Select a course</option>
                                                @foreach($student['recommendedCourses'] as $course)
                                                    <option value="{{ $course['id'] }}">
                                                        {{ $course['name'] }} ({{ $course['code'] }})
                                                        - Matches: {{ implode(', ', $course['matching_plos']) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <p class="text-muted">No matching courses found</p>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-primary send-recommendation"
                                                data-student-id="{{ $studentId }}">
                                            Send Recommendation
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    No students have failed PLOs in this course.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.send-recommendation').click(function() {
        const studentId = $(this).data('student-id');
        const courseId = $(this).closest('tr').find('.recommended-course').val();

        if (!courseId) {
            alert('Please select a course to recommend');
            return;
        }

        $.post('/api/send-course-recommendation', {
            course_id: courseId,
            student_id: studentId,
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                alert('Recommendation sent successfully!');
            } else {
                alert('Error sending recommendation: ' + response.message);
            }
        });
    });
});
</script>
@endsection 