<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseAllocation;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;
        
        // Get all course registrations for this student
        $courseRegistration = CourseRegistration::with([
            'student',
            'teacher',
            'course',
            'CourseAllocation.faculty',
            'courseAllocation.Course'
        ])->where('student_id', $user->id)->get();

        // Get approved and pending registrations
        $approvedRegistrations = $courseRegistration->where('status', 'approved');
        $pendingRegistrations = $courseRegistration->where('status', '!=', 'approved');

        // Get all course IDs that are already registered (either approved or pending)
        $registeredCourseIds = $courseRegistration->pluck('course_id')->toArray();

        // Get available courses excluding already registered ones
        $courses = CourseAllocation::with(['faculty.user', 'course'])
            ->where('batch', $student->batch)
            ->where('section', $student->section)
            ->whereNotIn('course_id', $registeredCourseIds)
            ->get();
        
        // For now, we'll skip course recommendations until the database is updated
        $courseRecommendations = collect();

        $coreuser = $user;
        return view('student.dashboard', compact(
            'courses', 
            'student', 
            'user', 
            'coreuser',
            'approvedRegistrations', 
            'pendingRegistrations',
            'courseRecommendations'
        ));
    }
}
