<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \App\Models\Faculty;
use App\Models\Role;
use \App\Models\course_content_point;
use \App\Models\course_content;
use \App\Models\course_outcome;
use \App\Models\course_detail;
use \App\Models\practical_outcome;
use \App\Models\Stu;
use Illuminate\Support\Facades\DB;
use \App\Models\Course;
use \App\Models\CourseAllocation;
use \App\Models\CourseRegistration;
use \App\Models\AdvisorClassAssignment;


class FacultyDashboardController extends Controller
{


    public function lecturarDashboard(){
        
        $user = Auth::user();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        $CourseAllocations = CourseAllocation::with(['course', 'faculty'])->where('faculty_id', $faculty->id)->get();
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;
    
        // dd($CourseAllocations);
        return view('lecturar.dashboard' , compact('primaryTasks', 'duties', 'dutyTasks' ,'designation' , 'CourseAllocations'));      
    }

    // In DashboardController.php
    public function showDutyDashboard(Role $duty)
    {
        abort_if($duty->type !== 'duty', 403);
       
        $user = Auth::user();
        $courses = Course::all();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;

        
        $ProgramCourseAllocations = CourseAllocation::with(['course', 'faculty'])->get();
        $advisorId = $user->id; // Logged in advisor



        // dd($ProgramCourseAllocations);
        // Get all assigned batch-section pairs for the advisor
        $assignments = AdvisorClassAssignment::where('advisor_id', $advisorId)->get();

        if ($assignments->isEmpty()) {
            $CourseRegistration = collect(); // Return empty collection
        } else {
            $CourseRegistration = CourseRegistration::with([
                'studentDetails',
                'student',
                'teacher',
                'course',
                'CourseAllocation.faculty',
                'courseAllocation.Course'
            ])->whereHas('studentDetails', function ($query) use ($assignments) {
                $query->where(function ($q) use ($assignments) {
                    foreach ($assignments as $assignment) {
                        $q->orWhere(function ($subQuery) use ($assignment) {
                            $subQuery->where('batch', $assignment->batch)
                                     ->where('section', $assignment->section);
                        });
                    }
                });
            })->get()
              ->groupBy(function ($item) {
                  return $item->student->id;
              })
              ->map(function ($group) {
                  return $group->first(); // Take only the first record per student
              })
              ->values(); // Optional: reset the keys to 0-based index
        }

        // dd($CourseRegistration);                                                                             

        $courseslist = Course::whereIn('semester', [1])->get();
        // dd($courses);

        if($duty->id == 9){
            return view('lecturar.hod.dashboard' , compact('primaryTasks', 'duties', 'dutyTasks' ,'designation' ,'courseslist')); 
        }elseif($duty->id == 10){
            return view('lecturar.program_manager.dashboard' , compact('primaryTasks', 'duties', 'dutyTasks' ,'designation' ,'ProgramCourseAllocations')); 
        }elseif($duty->id == 11){
            return view('lecturar.advisor.dashboard' , compact('primaryTasks', 'duties', 'dutyTasks' ,'designation' ,'courses' ,'ProgramCourseAllocations' ,'CourseRegistration')); 
        }else{
            return redirect()->back()->with('error', 'Student and User deleted successfully!');
        }
    }

    public function getCoursesBySemesterhod(Request $request) {
        $selectedSemesters = $request->input('semesters', []);
        
        $courses = empty($selectedSemesters) 
            ? Course::all() 
            : Course::whereIn('semester', $selectedSemesters)->get();
    
            // dd($courses);
        return response()->json($courses); // Direct return, no data wrapper
    }

    public function courselist_detail_hod($id){
        
        $user = Auth::user();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;
        $courses_detail = Course::with(  'course_detail','course_outcome','course_content','course_content_point','practical_outcome')->where('id', $id)->first();
        // dd($courses_detail);
        return view('lecturar.hod.course_detail' , compact('primaryTasks', 'duties', 'dutyTasks' ,'designation' ,'courses_detail'));      
    }

    public function updateCourseDetails(Request $request, $id)
    {
        $course = Course::with(  'course_detail','course_outcome','course_content','course_content_point','practical_outcome')->findOrFail($id);
        // dd($course);
        // dd($request->all());
        // dd($course->course_detail->first());
        // Update intro/objectives (if editable via input field)
        if ($request->has('intro_objectives')) {
            $course->course_detail->first()->update([
                'intro_objectives' => $request->input('intro_objectives'),
            ]);
        }
        // $courseOutcome = $course->course_outcome[0];
        // dd($courseOutcome);

        // dd($update);
        // Update course outcomes
        if ($request->has('course_outcomes')) {
            foreach ($request->input('course_outcomes') as $index => $outcome) {
                $courseOutcome = $course->course_outcome[$index] ?? null;
                if ($courseOutcome) {
                    $courseOutcome->update([
                        'clo' => $outcome['clo'],
                        'description' => $outcome['description'],
                        "blooms_level" => $outcome['bloom'],
                        'PLO' => $outcome['plo'],
                    ]);
                }
            }
        }

        if ($request->has('course_contents')) {
            foreach ($request->input('course_contents') as $cIndex => $contentData) {
                $content = $course->course_content[$cIndex] ?? null;
                if ($content) {
                    $content->update([
                        'heading_number' => $contentData['heading_number'],
                        'heading_title' => $contentData['heading_title'],
                    ]);
        
                    // Update associated points
                    if (isset($contentData['points'])) {
                        $points = $course->course_content_point->where('course_contents_id', $content->id)->values();
        
                        foreach ($contentData['points'] as $pIndex => $pointDescription) {
                            $point = $points[$pIndex] ?? null;
                            if ($point) {
                                $point->update([
                                    'description' => $pointDescription,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        if ($request->has('practical_outcomes')) {
            foreach ($request->input('practical_outcomes') as $pIndex => $practical) {
                $practicalOutcome = $course->practical_outcome[$pIndex] ?? null;
                if ($practicalOutcome) {
                    $practicalOutcome->update([
                        'clo' => $practical['clo'],
                        'description' => $practical['description'],
                        "blooms_level" => $practical['bloom'],
                        'PLO' => $practical['plo'],
                    ]);
                }
            }
        }
        

        // Same logic can be applied to practical outcomes and course content if editable.

        return redirect()->back()->with('success', 'Course details updated successfully.');
    }



    public function courselist_detail_add(){
        
        $user = Auth::user();
        $Course = Course::all();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        $CourseAllocations = CourseAllocation::with(['course', 'faculty'])->where('faculty_id', $faculty->id)->get();
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;
    
        // dd($CourseAllocations);
        return view('lecturar.hod.course_detail_store' , compact('primaryTasks','Course', 'duties', 'dutyTasks' ,'designation' , 'CourseAllocations'));      
    }


    public function storeCourseDetails(Request $request) 
    {
        $courseId = $request->course_id;
        // dd($request->all());
        DB::beginTransaction();
        try {
            $course = Course::findOrFail($courseId);

            // 1. Store Course Detail
            $courseDetail = course_detail::create([
                'course_id' => $courseId,
                'title' =>  $request->course_name,
                'intro_objectives' => $request->input('intro_objectives'),
            ]);
            // dd($courseDetail);
            // 2. Store Course Outcomes
            if ($request->has('course_outcomes')) {
                foreach ($request->input('course_outcomes') as $outcome) {
                    course_outcome::create([
                        'course_id' => $courseId,
                        'clo' => $outcome['clo'],
                        'course_details_id' => $courseDetail->id,
                        'description' => $outcome['description'],
                        "blooms_level" => $outcome['bloom'],
                        'PLO' => $outcome['plo'],
                    ]);
                }
            }

            // 3. Store Course Content and Points
            if ($request->has('course_contents')) {
                foreach ($request->input('course_contents') as $contentData) {
                    $content = course_content::create([
                        'course_id' => $courseId,
                        'heading_number' => $contentData['heading_number'],
                        'heading_title' => $contentData['heading_title'],
                    ]);

                    if (isset($contentData['points'])) {
                        foreach ($contentData['points'] as $pointDesc) {
                            course_content_point::create([
                                'course_id' => $courseId,
                                'course_contents_id' => $content->id,
                                'description' => $pointDesc,
                            ]);
                        }
                    }
                }
            }

            // 4. Store Practical Outcomes
            if ($request->has('practical_outcomes')) {
                foreach ($request->input('practical_outcomes') as $practical) {
                    practical_outcome::create([
                        'course_id' => $courseId,
                        'clo' => $practical['clo'],
                        'description' => $practical['description'],
                        "blooms_level" => $practical['bloom'],
                        'PLO' => $practical['plo'],
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Course details stored successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error storing course details: ' . $e->getMessage());
        }
    }
    

}
