@extends('lecturar.dashboard')

@section('title', 'PLO Counseling')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>PLO Counseling</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Course Name</th>
                                    <th>Failed PLO</th>
                                    <th>Student Name</th>
                                    <th>Recommended Course</th>
                                    <th>Status</th>
                                    <th>Send Recommendation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                    @if(isset($failedPLOsByCourse[$course->id]['students']) && count($failedPLOsByCourse[$course->id]['students']) > 0)
                                        @foreach($failedPLOsByCourse[$course->id]['students'] as $student)
                                            <tr>
                                                <td>{{ $course->name }}</td>
                                                <td>
                                                    @if(isset($student['failed_plos']) && is_array($student['failed_plos']))
                                                        @php
                                                            // Remove percentage if present, only show PLO number
                                                            $ploNumbers = array_map(function($plo) {
                                                                return preg_replace('/\s*\(.*\)/', '', $plo);
                                                            }, $student['failed_plos']);
                                                        @endphp
                                                        {{ implode(', ', $ploNumbers) }}
                                                    @endif
                                                </td>
                                                <td>{{ $student['name'] ?? ($student->name ?? 'Unknown') }}</td>
                                                <td>
                                                    @php
                                                        $recommended = [];
                                                        $availableCourses = [];
                                                        if(isset($failedPLOsByCourse[$course->id]['recommended_courses'])) {
                                                            foreach($student['failed_plos'] as $failedPlo) {
                                                                $ploKey = preg_replace('/\s*\(.*\)/', '', $failedPlo);
                                                                if(isset($failedPLOsByCourse[$course->id]['recommended_courses'][$ploKey])) {
                                                                    foreach($failedPLOsByCourse[$course->id]['recommended_courses'][$ploKey] as $recCourse) {
                                                                        $availableCourses[$ploKey][] = $recCourse;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if(!empty($availableCourses))
                                                        <form method="POST" action="{{ route('sendCourseRecommendation') }}" class="d-flex align-items-center gap-2">
                                                            @csrf
                                                            <input type="hidden" name="student_id" value="{{ $student['student_id'] }}">
                                                            <input type="hidden" name="course_id" value="{{ $student['course_id'] }}">
                                                            <select name="recommended_course_id" class="form-select form-select-sm w-auto" required>
                                                                <option value="">Select Course</option>
                                                                @foreach($availableCourses as $ploKey => $coursesList)
                                                                    @foreach($coursesList as $recCourse)
                                                                        <option value="{{ $recCourse->id ?? $recCourse['id'] }}">
                                                                            {{ $recCourse->name ?? $recCourse['name'] }} ({{ $ploKey }})
                                                                        </option>
                                                                    @endforeach
                                                                @endforeach
                                                            </select>
                                                            <button type="submit" class="btn btn-primary btn-sm">Send</button>
                                                        </form>
                                                    @else
                                                        <span class="text-danger">No Course</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($student['recommendation_status']))
                                                        @if($student['recommendation_status'] === 'pending')
                                                            <span class="badge bg-warning text-dark">Pending</span>
                                                        @elseif($student['recommendation_status'] === 'accepted')
                                                            <span class="badge bg-success">Accepted</span>
                                                        @elseif($student['recommendation_status'] === 'rejected')
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @else
                                                            <span class="badge bg-secondary">None</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">None</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($availableCourses))
                                                        <!-- Button is inside the form above -->
                                                    @else
                                                        <button class="btn btn-secondary btn-sm" disabled>Send</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
