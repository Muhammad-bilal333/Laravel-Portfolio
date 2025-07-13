<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\CourseRegistration;

class CourseRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'student_id' => 'required|exists:students,user_id',
            'selected_courses' => 'required|array|min:1',
            'selected_courses.*' => 'required|integer',
            'course_ids' => 'required|array',
            'course_ids.*' => 'required|integer|exists:courses,id',
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'required|integer|exists:users,id',
            'course_allocation_ids' => 'required|array',
            'course_allocation_ids.*' => 'required|integer|exists:course_allocations,id',
        ]);
    

        $selectedCourses = $request->input('selected_courses');
        $teacherIds = $request->input('teacher_ids');
        $courseIds = $request->input('course_ids');
        $allocationIds = $request->input('course_allocation_ids');
        $studentId = $request->input('student_id');

        // Get all already registered courses for this student
        $existingRegistrations = CourseRegistration::where('student_id', $studentId)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->pluck('course_id')
            ->toArray();

        $errors = [];
        $successCount = 0;

        foreach ($selectedCourses as $courseAllocationId) {
            $index = array_search($courseAllocationId, $allocationIds);
            
            if ($index !== false) {
                $currentCourseId = $courseIds[$index];
                
                // Check if course is already registered
                if (in_array($currentCourseId, $existingRegistrations)) {
                    $errors[] = "Course ID {$currentCourseId} is already registered";
                    continue;  // Skip this iteration
                }
                
                // Create new registration
                CourseRegistration::create([
                    'student_id' => $studentId,
                    'course_id' => $currentCourseId,
                    'teacher_id' => $teacherIds[$index],
                    'cource_allocation_id' => $allocationIds[$index], 
                    'status' => 'pending',
                ]);
                
                $successCount++;
                // Add to existing registrations to prevent duplicate in same request
                $existingRegistrations[] = $currentCourseId;
            }
        }

        // Return appropriate response
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }
        // $selectedCourses = $request->input('selected_courses');
        // $teacherIds = $request->input('teacher_ids');
        // $courseIds = $request->input('course_ids');
        // $allocationIds = $request->input('course_allocation_ids');
        // $studentId = $request->input('student_id');
    
        // foreach ($selectedCourses as $index => $courseAllocationId) {
        //     CourseRegistration::create([
        //         'student_id' => $studentId,
        //         'course_id' => $courseIds[$index],
        //         'teacher_id' => $teacherIds[$index],
        //         'cource_allocation_id' => $allocationIds[$index], 
        //         'status' => 'pending',
        //     ]);
        // }



        // $selectedCourses = $request->input('selected_courses');
        // $teacherIds = $request->input('teacher_ids');
        // $courseIds = $request->input('course_ids');
        // $allocationIds = $request->input('course_allocation_ids');
        // $studentId = $request->input('student_id');

        // foreach ($selectedCourses as $courseAllocationId) {
        //     // Find the index of the selected course in the allocationIds array
        //     $index = array_search($courseAllocationId, $allocationIds);
            
        //     if ($index !== false) {
        //         CourseRegistration::create([
        //             'student_id' => $studentId,
        //             'course_id' => $courseIds[$index],
        //             'teacher_id' => $teacherIds[$index],
        //             'cource_allocation_id' => $allocationIds[$index], 
        //             'status' => 'pending',
        //         ]);
        //     }
        // }



        // $selectedCourses = $request->input('selected_courses'); // array of selected allocation IDs
        // $courseData = $request->input('course_data'); // associative array
        // $studentId = $request->input('student_id');

        // foreach ($selectedCourses as $courseAllocationId) {
        //     if (!isset($courseData[$courseAllocationId])) {
        //         continue; // skip if missing
        //     }

        //     $data = $courseData[$courseAllocationId];

        //    $CourseRegistration = CourseRegistration::create([
        //         'student_id' => $studentId,
        //         'course_id' => $data['course_id'],
        //         'teacher_id' => $data['teacher_id'],
        //         'cource_allocation_id' => $data['allocation_id'],
        //         'status' => 'pending',
        //     ]);
        //     dd($CourseRegistration);
        // }
        




        // $selectedCourses = $request->input('selected_courses');
        // $teacherIds = $request->input('teacher_ids');
        // $courseIds = $request->input('course_ids');
        // $allocationIds = $request->input('course_allocation_ids');
        // // dd($allocationIds);
        // $studentId = $request->input('student_id');

        // // Loop through selected courses and save to the database
        // foreach ($selectedCourses as $index => $courseAllocationId) {
        //     CourseRegistration::create([
        //         'student_id' => $studentId,
        //         'course_id' => $courseIds[$index],
        //         'teacher_id' => $teacherIds[$index],
        //         'cource_allocation_id' => $allocationIds[$index], 
        //         'status' => 'pending',
        //     ]);
        // }

        return redirect()->back()->with('success', 'Courses registered successfully!');
    }
}
