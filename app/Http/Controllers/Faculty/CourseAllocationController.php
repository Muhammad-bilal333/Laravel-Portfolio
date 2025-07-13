<?php


namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \App\Models\Faculty;
use App\Models\Role;
use App\Models\failPLO;


use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;


use \App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use \App\Models\CourseAllocation;
use \App\Models\CourseRegistration;
use \App\Models\assessment;
use \App\Models\assessment_clo_detail;
use App\Models\User;
use App\Models\LabAssessmentRubricDetail;
use App\Models\LabAssessment;
use App\Models\practical_outcome;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class CourseAllocationController extends Controller
{

    public function SingleStuCources($id)
    {


        $user = Auth::user();
        $courses = Course::all();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;

        
        $ProgramCourseAllocations = CourseAllocation::with(['course', 'faculty'])->get();

        // dd($request->all());
        $CourseRegistration = CourseRegistration::with([
            'studentDetails',
            'student',
            'teacher',
            'course',
            'CourseAllocation.faculty',
            'courseAllocation.Course'
        ])->where('student_id' , $id )->get();

        // dd($ProgramCourseAllocations);

        return view('lecturar.advisor.single_stu_cources' , compact('primaryTasks', 'duties', 'dutyTasks' ,'designation' ,'courses' ,'ProgramCourseAllocations' ,'CourseRegistration')); 
    }

    public function plo_counseling($id)
    {
       $user = Auth::user();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;

        // Get all courses the faculty is teaching (or all courses if needed)
        $courses = Course::all();
        $failedPLOsByCourse = [];

        foreach ($courses as $course) {
            // Get all students registered in this course
            $courseRegistrations = CourseRegistration::where('course_id', $course->id)->get();
            $studentIds = $courseRegistrations->pluck('student_id')->unique();
            $studentsData = assessment_clo_detail::with(['assessment.student', 'assessment.studentdetail'])
                ->whereHas('assessment', function($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->get();

            if ($studentsData->isEmpty()) continue;

            // Group assessments by student
            $groupedStudents = [];
            foreach ($studentsData as $assessment) {
                $student_id = $assessment->assessment->student_id ?? null;
                if ($student_id) {
                    $groupedStudents[$student_id][] = $assessment;
                }
            }

            // Fetch all PLO names for this course
            $ploNames = \App\Models\practical_outcome::where('course_id', $course->id)->pluck('PLO')->unique()->values()->toArray();
            if (empty($ploNames)) {
                $ploNames = ['PLO-1', 'PLO-2', 'PLO-3', 'PLO-4']; // fallback
            }

            foreach ($studentIds as $student_id) {
                $assessments = $groupedStudents[$student_id] ?? [];
                if (empty($assessments)) continue;

                $assignments = array_fill(1, 4, 0);
                $quizzes     = array_fill(1, 4, 0);
                $midterms    = array_fill(1, 4, 0);
                $finals      = array_fill(1, 4, 0);

                foreach ($assessments as $assessment) {
                    $clo = $assessment->clo_number ?? 'N/A';
                    $obtained = $assessment->obtained_marks ?? 0;
                    $type = strtolower($assessment->assessment->type ?? '');
                    $cloNumber = (int)str_replace('CLO', '', $clo);
                    switch ($type) {
                        case 'assignment':
                            if (isset($assignments[$cloNumber])) $assignments[$cloNumber] = $obtained;
                            break;
                        case 'quiz':
                            if (isset($quizzes[$cloNumber])) $quizzes[$cloNumber] = $obtained;
                            break;
                        case 'mid':
                            if (isset($midterms[$cloNumber])) $midterms[$cloNumber] = $obtained;
                            break;
                        case 'final':
                            if (isset($finals[$cloNumber])) $finals[$cloNumber] = $obtained;
                            break;
                    }
                }

                $assignmentCount = count(array_filter($assignments, fn($v) => $v > 0));
                $quizCount       = count(array_filter($quizzes, fn($v) => $v > 0));
                $totalAssignment = array_sum($assignments);
                $totalQuiz       = array_sum($quizzes);
                $assignmentAvg = $assignmentCount > 0 ? ($totalAssignment / ($assignmentCount))  : 0;
                $quizAvg       = $quizCount > 0 ? ($totalQuiz / ($quizCount)) : 0;
                $totalAQAvg    = $assignmentAvg + $quizAvg;
                $totalMid        = array_sum($midterms);
                $totalFinal      = array_sum($finals);
                $grandTotal      = $totalAQAvg + $totalMid + $totalFinal;

                // Calculate CLO percentages
                $clos = [];
                for ($i = 1; $i <= 4; $i++) {
                    $assignmentMarks = $assignments[$i] ?? 0;
                    $quizMarks       = $quizzes[$i] ?? 0;
                    $midMarks        = $midterms[$i] ?? 0;
                    $finalMarks      = $finals[$i] ?? 0;
                    $obtained = $assignmentMarks + $quizMarks + $midMarks + $finalMarks;
                    $totalPossible = 15 + 10 + 5 + 10;
                    $cloPercentage = $obtained > 0 ? ($obtained / $totalPossible) * 100 : 0;
                    $clos[$i] = round($cloPercentage, 2);
                }

                // Calculate PLOs dynamically
                $studentPLOs = [];
                for ($i = 0; $i < count($ploNames); $i++) {
                    $cloA = $clos[$i + 1] ?? 0;
                    $cloB = $clos[$i + 2] ?? 0;
                    $studentPLOs[$ploNames[$i]] = round((($cloA + $cloB) / 2), 2);
                }

                // Collect failed PLOs
                $failedPLOs = [];
                foreach ($studentPLOs as $ploName => $ploValue) {
                    if ($ploValue < 50) {
                        $failedPLOs[] = $ploName . ' (' . $ploValue . '%)'; // Show PLO name and percentage
                    }
                }
                $student = $assessments[0]->assessment->student;
                $failedPLOsByCourse[$course->id]['students'][$student_id] = [
                    'id' => $student_id,
                    'name' => $student->name ?? 'Unknown',
                    'failed_plos' => !empty($failedPLOs) ? $failedPLOs : ['-'],
                    'recommendation_status' => null,
                    'student_id' => $student_id,
                    'course_id' => $course->id,
                ];
            }
        }

        // Attach recommended courses for each failed PLO
        foreach ($failedPLOsByCourse as $courseId => &$courseData) {
            $course = Course::find($courseId);
            $nextSemesterCourses = Course::where('semester', $course ? $course->semester + 1 : 0)->get();
            $recommendedCourses = [];
            foreach ($courseData['students'] ?? [] as $student) {
                foreach ($student['failed_plos'] as $failedPlo) {
                    foreach ($nextSemesterCourses as $recCourse) {
                        $recPlos = practical_outcome::where('course_id', $recCourse->id)->pluck('PLO')->toArray();
                        if (in_array($failedPlo, $recPlos)) {
                            $recommendedCourses[$failedPlo][] = $recCourse;
                        }
                    }
                }
            }
            $failedPLOsByCourse[$courseId]['recommended_courses'] = $recommendedCourses;
        }
        unset($courseData);

        return view('lecturar.advisor.plo_counseling', compact(
            'courses',
            'failedPLOsByCourse',
            'designation',
            'duties'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'status' => 'required|string|in:approved,rejected,pending' // Update status options as needed
        ]);

        // Find the course registration by ID
        $courseRegistration = CourseRegistration::findOrFail($id);

        // Update the status field
        $courseRegistration->status = ucfirst($validated['status']); // Ensure the status is capitalized
        $courseRegistration->save(); // Save the changes

        // Return a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Status updated successfully.',
            'new_status' => $courseRegistration->status // Returning the updated status
        ]);
    }

    public function AllRegisteredStudent($id)
    {
        $course_id = $id;
        $user = Auth::user();
        $faculty_id =  $user->id;
        $courses = Course::all();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;

        
        $ProgramCourseAllocations = CourseAllocation::with(['course', 'faculty'])->get();

        // dd($request->all());
        $CourseRegistration = CourseRegistration::with([
            'studentDetails',
            'student',
            'teacher',
            'course',
            'CourseAllocation.faculty',
            'courseAllocation.Course'
        ])->where('course_id' , $course_id)->where('teacher_id',$faculty_id)->where('status' , 'approved')->get();

        // dd($CourseRegistration);

        // return view('lecturar.add_marks' , compact('primaryTasks', 'duties', 'dutyTasks' ,'designation' ,'courses' ,'ProgramCourseAllocations' ,'CourseRegistration')); 
        // return view('lecturar.add_marks_copy' , compact('primaryTasks', 'duties', 'dutyTasks' ,'designation' ,'courses' ,'ProgramCourseAllocations' ,'CourseRegistration')); 
        return view('lecturar.All_registered_student' , compact('primaryTasks', 'duties', 'dutyTasks' ,'designation' ,'courses' ,'ProgramCourseAllocations' ,'CourseRegistration')); 
    }

    public function student_marks($id)
    {
        // $course_id = $id;
        $user = Auth::user();
        $faculty_id =  $user->id;
        $courses = Course::all();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;
        $ProgramCourseAllocations = CourseAllocation::with(['course', 'faculty'])->get();
        $marksDetail = assessment::with([
            'marks'
        ])->where('student_id', $id)->get();

        return view('lecturar.show_student_marks' , compact('primaryTasks', 'id', 'duties', 'dutyTasks' ,'designation' ,'courses' ,'ProgramCourseAllocations' ,'marksDetail')); 
    }
    public function student_marks_lab($id)
    {
        // $course_id = $id;
        $user = Auth::user();
        $faculty_id =  $user->id;
        $courses = Course::all();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;
        $ProgramCourseAllocations = CourseAllocation::with(['course', 'faculty'])->get();
        $marksDetail = LabAssessment::with([
            'marks'
        ])->where('student_id', $id)->get();
        // $marksDetail = LabAssessment::where('student_id', $id)->get();
        // dd($marksDetail);
        return view('lecturar.show_student_marks' , compact('primaryTasks', 'id', 'duties', 'dutyTasks' ,'designation' ,'courses' ,'ProgramCourseAllocations' ,'marksDetail')); 
    }

    public function add_student_marks($id)
    {
        // $course_id = $id;
        $course_id = $id;
        $user = Auth::user();
        $faculty_id =  $user->id;
        $courses = Course::all();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;

        
        $ProgramCourseAllocations = CourseAllocation::with(['course', 'faculty'])->get();

        // dd($request->all());
        $CourseRegistration = CourseRegistration::with([
            'studentDetails',
            'student',
            'teacher',
            'course',
            'CourseAllocation.faculty',
            'courseAllocation.Course'
        ])->where('course_id' , $course_id)->where('teacher_id',$faculty_id)->where('status' , 'approved')->get();

     
        return view('lecturar.add_marks_copy' , compact( 'CourseRegistration' ,'primaryTasks','id', 'duties', 'dutyTasks' ,'designation' ,'courses' ,'ProgramCourseAllocations')); 
    }
    public function add_studentlab_marks($id)
    {
        // $course_id = $id;
        $course_id = $id;
        $user = Auth::user();
        $faculty_id =  $user->id;
        $courses = Course::all();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation;
        $duties = Role::whereIn('id', json_decode($faculty->duties))->get();
        $primaryTasks = Role::find($user->role_id)->tasks;
        $dutyTasks = $duties->flatMap->tasks;

        
        $ProgramCourseAllocations = CourseAllocation::with(['course', 'faculty'])->get();

        // dd($request->all());
        $CourseRegistration = CourseRegistration::with([
            'studentDetails',
            'student',
            'teacher',
            'course',
            'CourseAllocation.faculty',
            'courseAllocation.Course'
        ])->where('course_id' , $course_id)->where('teacher_id',$faculty_id)->where('status' , 'approved')->get();

     
        return view('lecturar.add_marks_lab' , compact( 'CourseRegistration' ,'primaryTasks','id', 'duties', 'dutyTasks' ,'designation' ,'courses' ,'ProgramCourseAllocations')); 
    }

    public function store_student_marks(Request $request)
    {
        DB::beginTransaction();
    
        try {
            foreach ($request->student_ids as $studentId) {
                // Create assessment for each student
                $assessment = Assessment::create([
                    'course_id' => $request->course_id,
                    'teacher_id' => Auth::id(),
                    'student_id' => $studentId,
                    'type' => $request->type,
                    'assessment_title' => $request->title,
                ]);
    
                // Create assessment CLO detail (no student_id needed now)
                assessment_clo_detail::create([
                    'assessment_id' => $assessment->id,
                    'clo_number' => $request->clo_number_single,
                    'total_marks' => $request->total_marks_single,
                    'obtained_marks' => $request->obtained_marks_single[$studentId],
                ]);
            }
    
            DB::commit();
            return redirect()->back()->with('success', 'Marks saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save marks. Error: ' . $e->getMessage());
        }
    }

    public function store_student_marks_lab(Request $request)
        {
            // dd($request->all());

            DB::beginTransaction();

                try {
                    foreach ($request->student_ids as $studentId) {
                        // Create lab assessment
                        $labAssessment = LabAssessment::create([
                            'course_id' => $request->course_id,
                            'teacher_id' => Auth::id(),
                            'student_id' => $studentId,
                            'type' => $request->type,
                            'assessment_title' => $request->title,
                        ]);

                        // Create rubric detail
                        LabAssessmentRubricDetail::create([
                            'lab_assessment_id' => $labAssessment->id,
                            'rubric_number' => $request->R1_number_single,
                            'clo_number' => $request->clo_number_single,
                            'total_marks' => $request->total_marks_single,
                            'obtained_marks' => $request->obtained_marks_single[$studentId],
                        ]);
                    }

                    DB::commit();
                    
                    return redirect()->back()
                        ->with('success', 'Lab assessment saved successfully!');
                        
                } catch (\Exception $e) {
                    
                    return redirect()->back()
                        ->with('error', 'Failed to save lab assessment. Error: '.$e->getMessage())
                        ->withInput();
                }
        }
    
    
        public function setCourseSession($course_id)
        {
            // Clear any existing course_id
            Session::forget('course_id');

            // Store the new course_id
            Session::put('course_id', $course_id);

            // Redirect to the page that shows registered students
            return redirect()->route('courses.AllRegisteredStudent' ,  $course_id);
        }
        public function delete_student_marks($id)
        {
            try {
                DB::beginTransaction();
                
                // First try to find and delete a regular assessment
                $assessment = assessment::find($id);
                if ($assessment) {
                    // Delete CLO details first
                    assessment_clo_detail::where('assessment_id', $id)->delete();
                    // Then delete the assessment
                    $assessment->delete();
                    DB::commit();
                    return redirect()->back()->with('success', 'Assessment deleted successfully.');
                }
                
                // If not found, try to find and delete a lab assessment
                $labAssessment = LabAssessment::find($id);
                if ($labAssessment) {
                    // Delete lab assessment rubric details first
                    LabAssessmentRubricDetail::where('lab_assessment_id', $id)->delete();
                    // Then delete the lab assessment
                    $labAssessment->delete();
                    DB::commit();
                    return redirect()->back()->with('success', 'Lab assessment deleted successfully.');
                }
                
                // If neither type of assessment is found
                DB::rollBack();
                return redirect()->back()->with('error', 'Assessment not found.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Error deleting assessment: ' . $e->getMessage());
            }
        }



        public function exportToExcel(Request $request) {
         
                    // ... [Your existing data fetching code] ...

                

                    $greenStyle = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FF00FF00'] // Green
                        ]
                    ];
                    $PLO = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'd4d9dc'] // Green
                        ]
                    ];
                    $FinaltotalColor = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'b28c14'] // blue for assignment
                        ]
                    ];
                    $assignmentColor = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'b8cce4'] // blue for assignment
                        ]
                    ];
                    $quizColor = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'd7e4bc'] // Greenish  for quiz
                        ]
                    ];
                    $MidColor = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'fac090'] // skin for Mid 
                        ]
                    ];
                    $finalColor = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'ccc0da'] // purpul for final 
                        ]
                    ];



                    $CLO1Color = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'ffff99'] // CLO1
                        ]
                    ];
                    $CLO2Color = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'ff65d7'] // CLO2
                        ]
                    ];
                    $CLO3Color = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'b6dde8'] // CLO3
                        ]
                    ];
                    $CLO4Color = [
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => '99ff66'] // CLO4
                        ]
                    ];
                
                    $course_id = $request->course_id;
                    $faculty_id = $request->faculty_id;

                    // Fetch related data
                    $assessments = Assessment::with(['marks', 'student' ,'studentdetail'])
                        ->where('course_id', $course_id)
                        ->where('teacher_id', $faculty_id)
                        ->get();




                    $studentsData = assessment_clo_detail::with(['assessment.student', 'assessment.studentdetail'])
                    ->whereHas('assessment', function($query) use ($course_id, $faculty_id) {
                        $query->where('course_id', $course_id)
                                ->where('teacher_id', $faculty_id);
                    })
                    ->get();

                    $batchstudent = ($studentsData->first());
                        
                        // dd($assessments);
                        $course = Course::find($course_id);
                        $faculty = User::find($faculty_id);
                        // dd($faculty);
                        $courseName = $course->name ?? 'empty';
                        $facultyName = $faculty->name ?? 'empty';
                        $batch = $batchstudent->assessment->studentdetail->batch ?? 'empty';
                        $section = $batchstudent->assessment->studentdetail->section ?? 'empty';
                        // dd($section , $batch);
                        // Group assessments by student
                        $groupedAssessments = $assessments->groupBy('student_id');
                    // Create a new Spreadsheet
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                
                    // Merge cells for Institute Name (Row 1)
                    $sheet->mergeCells('A1:AD1'); // Merges 29 columns (A to AD)
                    $sheet->setCellValue('A1', 'Foundation University Islamabad, Rawalpindi Campus');
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                    // Merge cells for Department (Row 2)
                    $sheet->mergeCells('A2:AD2');
                    $sheet->setCellValue('A2', 'Department of Engineering Technology');
                    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                    // Merge cells for Course Info (Row 3)
                    $sheet->mergeCells('A3:AD3');
                    $sheet->setCellValue('A3', "Course: {$courseName} - Instructor: {$facultyName} - Class and Section: {$batch} - {$section} ");
                    $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                

                      $groupedStudents = [];

                    foreach ($studentsData as $assessment) {
                        $student_id = $assessment->assessment->student_id ?? null;
                        if ($student_id) {
                            $groupedStudents[$student_id][] = $assessment;
                        }
                    }

                    $quizCLO = array_fill(0, 4, '');
                    $assignmentCLO = array_fill(0, 4, '');
                    $midCLO = array_fill(0, 4, '');
                    $finalCLO = array_fill(0, 4, '');

                    // Process only the first student
                    foreach ($groupedStudents as $student_id => $assessments) {
                        $quizIndex = $assignmentIndex = $midIndex = $finalIndex = 0;

                        foreach ($assessments as $assessment) {
                            $clo = $assessment->clo_number ?? 1;
                            $title = strtolower($assessment->assessment->type ?? 'quiz');

                            $cloLabel = 'CLO' . $clo;

                            if (stripos($title, 'quiz') !== false && $quizIndex < 4) {
                                $quizCLO[$quizIndex++] = $cloLabel;
                            } elseif (stripos($title, 'assignment') !== false && $assignmentIndex < 4) {
                                $assignmentCLO[$assignmentIndex++] = $cloLabel;
                            } elseif (stripos($title, 'mid') !== false && $midIndex < 4) {
                                $midCLO[$midIndex++] = $cloLabel;
                            } elseif ($finalIndex < 4) {
                                $finalCLO[$finalIndex++] = $cloLabel;
                            }
                        }

                        // Only process first student
                        break;
                    }

                    // Combine CLO row for output
                    $finalCloRow = array_merge(
                        ['', '', ''],         // A-row offset
                        $assignmentCLO,       // Assignment CLOs
                        $quizCLO,             // Quiz CLOs
                        [''],                 // Static placeholder after assignments
                        $midCLO,              // Mid CLOs
                        [''],                 // Static placeholder after mids
                        $finalCLO,            // Final CLOs
                        ['']                  // Static placeholder after finals
                    );

                    // Debug to verify CLO row
                    // dd($finalCloRow);
                    $sheet->mergeCells('A4:C4');
                    $sheet->fromArray($finalCloRow, null, 'A4');
                    $sheet->setCellValue('A4', 'CLO No');
                    $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $columnIndex = 4; // Start from column D (i.e., index 4 as A=1)

                    foreach ($finalCloRow as $key => $cloLabel) {
                        if ($key < 3) continue; // Skip A, B, C (first three cells)

                        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
                        $cell = $columnLetter . '4';

                        switch ($cloLabel) {
                            case 'CLO1':
                                $sheet->getStyle($cell)->applyFromArray($CLO1Color);
                                break;
                            case 'CLO2':
                                $sheet->getStyle($cell)->applyFromArray($CLO2Color);
                                break;
                            case 'CLO3':
                                $sheet->getStyle($cell)->applyFromArray($CLO3Color);
                                break;
                            case 'CLO4':
                                $sheet->getStyle($cell)->applyFromArray($CLO4Color);
                                break;
                            default:
                                // No styling for empty or unrecognized cells
                                break;
                        }

                        $columnIndex++;
                    }
                    


                    // $sheet->getStyle('D4')->applyFromArray($CLO1Color);
                    // $sheet->getStyle('E4')->applyFromArray($CLO2Color);
                    // $sheet->getStyle('F4')->applyFromArray($CLO3Color);
                    // $sheet->getStyle('G4')->applyFromArray($CLO4Color);

                    // $sheet->getStyle('H4')->applyFromArray($CLO1Color); // Assignment CLO1
                    // $sheet->getStyle('I4')->applyFromArray($CLO2Color); // Assignment CLO1
                    // $sheet->getStyle('J4')->applyFromArray($CLO3Color); // Assignment CLO1
                    // $sheet->getStyle('K4')->applyFromArray($CLO4Color);
                    // // Assignment CLO1
                    // $sheet->getStyle('M4')->applyFromArray($CLO1Color); // Assignment CLO1
                    // $sheet->getStyle('N4')->applyFromArray($CLO2Color); // Assignment CLO1
                    // $sheet->getStyle('O4')->applyFromArray($CLO3Color); // Assignment CLO1
                    // $sheet->getStyle('P4')->applyFromArray($CLO4Color); // Assignment CLO1
                    
                    // $sheet->getStyle('R4')->applyFromArray($CLO1Color); // Assignment CLO1
                    // $sheet->getStyle('S4')->applyFromArray($CLO2Color); // Assignment CLO1
                    // $sheet->getStyle('T4')->applyFromArray($CLO3Color); // Assignment CLO1
                    // $sheet->getStyle('U4')->applyFromArray($CLO4Color); // Assignment CLO1



                    function colorBlockRange($sheet, $startCol, $endCol, $startRow, $endRow, $styleArray) {
                        for ($row = $startRow; $row <= $endRow; $row++) {
                            $range = "{$startCol}{$row}:{$endCol}{$row}";
                            $sheet->getStyle($range)->applyFromArray($styleArray);
                        }
                    }

                    colorBlockRange($sheet, 'B', 'F', 5, $sheet->getHighestRow(), $assignmentColor);
                
                    // Add CLO Numbers (Row 4)
                    $sheet->mergeCells('A5:C5');
                    $sheet->setCellValue('A5', 'Marks Catagory');
                    $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    
                    $sheet->mergeCells('D5:G5'); // Assignment (5 columns)
                    $sheet->setCellValue('D5', 'Assignment');
                    $sheet->getStyle('D5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D5:G5')->applyFromArray($assignmentColor);
                    $sheet->getStyle('D6:G6')->applyFromArray($assignmentColor);
                    $sheet->getStyle('D7:G7')->applyFromArray($assignmentColor);
                    $sheet->getStyle('D8:G8')->applyFromArray($assignmentColor);
                    

                    $sheet->mergeCells('H5:L5'); // Quiz (5 columns)
                    $sheet->setCellValue('H5', 'Quiz');
                    $sheet->getStyle('H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('H5:L5')->applyFromArray($quizColor);  
                    $sheet->getStyle('H6:L6')->applyFromArray($quizColor);  
                    $sheet->getStyle('H7:L7')->applyFromArray($quizColor);  
                    $sheet->getStyle('H8:L8')->applyFromArray($quizColor);  
                    
                    $sheet->mergeCells('M5:Q5'); // Midterm (5 columns)
                    $sheet->setCellValue('M5', 'Mid term Exam');
                    $sheet->getStyle('M5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('M5:Q5')->applyFromArray($MidColor);
                    $sheet->getStyle('M6:Q6')->applyFromArray($MidColor);
                    $sheet->getStyle('M7:Q7')->applyFromArray($MidColor);
                    $sheet->getStyle('M8:Q8')->applyFromArray($MidColor);

                    
                    $sheet->mergeCells('R5:U5'); // Final (5 columns)
                    $sheet->setCellValue('R5', 'Terminal Exam');
                    $sheet->getStyle('R5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('R5:V5')->applyFromArray($finalColor);
                    $sheet->getStyle('R6:V6')->applyFromArray($finalColor);
                    $sheet->getStyle('R7:V7')->applyFromArray($finalColor);
                    $sheet->getStyle('R8:V8')->applyFromArray($finalColor);
                    $sheet->getStyle('W8')->applyFromArray($finalColor);

                    $sheet->getStyle('X8')->applyFromArray($CLO1Color);
                    $sheet->getStyle('Y8')->applyFromArray($CLO2Color);
                    $sheet->getStyle('Z8')->applyFromArray($CLO3Color);
                    $sheet->getStyle('AA8')->applyFromArray($CLO4Color);

                    $sheet->getStyle('AC8:AE8')->applyFromArray($PLO);


                

                    $sheet->mergeCells('A6:C6');
                    $sheet->setCellValue('A6', 'Marks Weightage');
                    $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->fromArray(
                        ['15', '15', '15', '15','10', '10', '10', '10', '', '25', '25', '25', '25', '', '50', '50', '50', '50', ''],
                        null,
                        'D6'
                    );

                    
                   $quizMarks = array_fill(0, 4, 0);
                    $assignmentMarks = array_fill(0, 4, 0);
                    $midMarks = array_fill(0, 4, 0);
                    $finalMarks = array_fill(0, 4, 0);

                    // Process only the first student
                    foreach ($groupedStudents as $student_id => $assessments) {
                        $quizIndex = $assignmentIndex = $midIndex = $finalIndex = 0;

                        foreach ($assessments as $assessment) {
                            $marks = $assessment->total_marks ?? 10;
                            $title = strtolower($assessment->assessment->type ?? 'quiz');

                            if (stripos($title, 'quiz') !== false && $quizIndex < 4) {
                                $quizMarks[$quizIndex++] = $marks;
                            } elseif (stripos($title, 'assignment') !== false && $assignmentIndex < 4) {
                                $assignmentMarks[$assignmentIndex++] = $marks;
                            } elseif (stripos($title, 'mid') !== false && $midIndex < 4) {
                                $midMarks[$midIndex++] = $marks;
                            } elseif ($finalIndex < 4) {
                                $finalMarks[$finalIndex++] = $marks;
                            }
                        }

                        // Only process first student
                        break;
                    }

                    // Build the final row
                    $finalTotalMarksRow = array_merge(
                        ['', '', ''],            // A7, B7, C7
                        $assignmentMarks,        // Assignment marks (up to 4)
                        $quizMarks,              // Quiz marks (up to 4)
                        ['25'],                  // Static total for quiz + assignment
                        $midMarks,               // Mid-term marks (up to 4)
                        ['25'],                  // Static total for mid
                        $finalMarks,             // Final marks (up to 4)
                        ['50'],                  // Static total for final
                    );

                    // dd($finalTotalMarksRow);

                    // Set row 7 with Total Marks
                    $sheet->mergeCells('A7:C7');
                    $sheet->fromArray($finalTotalMarksRow, 0, 'A7');
                    $sheet->setCellValue('A7', 'Maximum Marks');
                    $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                    // $sheet->mergeCells('A7:C7');
                    // $sheet->setCellValue('A7', 'Maximum Marks');
                    // $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    // $sheet->fromArray(
                    //     ['10', '10', '10', '10','5', '5', '5', '5', '25', '5', '5', '5', '10', '25', '10', '10', '15', '15', 'Total'],
                    //     null,
                    //     'D7'
                    // );
        
                    $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->fromArray(
                        ['S.No.','Registration No','Name','A1','A2','A3', 'A4','Q1', 'Q2', 'Q3', 'Q4', 'Total', 'Mid-1', 'Mid-2', 'Mid-3', 'Mid-4', 'Total', 'Final-1', 'Final-2', 'Final-3', 'Final-4', 'Total','Final','CLO1','CLO2','CLO3','CLO4','','PLO1','PLO2','PLO3'],
                        null,
                        'A8'
                    );
                    $sheet->getColumnDimension('A')->setWidth(6);   // S.No.
                    $sheet->getColumnDimension('B')->setWidth(30);  // Registration No
                    $sheet->getColumnDimension('C')->setWidth(25);  // Name
                    

                    $rowIndex = 9;  
                    $sno = 1;  

                  
                    foreach ($groupedStudents as $student_id => $assessments) {
                        $firstAssessment = $assessments[0];
                        $student = $firstAssessment->assessment->student ?? null;
                        $studentdetail = $firstAssessment->assessment->studentdetail ?? null;

                        if (!$student || !$studentdetail) continue;

                        $reg_no = 'FUI/FURC/' . $studentdetail->batch . '-BSET-' . $studentdetail->roll_number;
                        $name = $student->name ?? 'N/A';

                        // Initialize arrays for CLO-based tracking
                        $clos = [
                            1 => ['quizzes' => 0, 'assignments' => 0, 'midterms' => 0, 'finals' => 0],
                            2 => ['quizzes' => 0, 'assignments' => 0, 'midterms' => 0, 'finals' => 0],
                            3 => ['quizzes' => 0, 'assignments' => 0, 'midterms' => 0, 'finals' => 0],
                            4 => ['quizzes' => 0, 'assignments' => 0, 'midterms' => 0, 'finals' => 0]
                        ];

                        // Initialize assessment arrays (for backward compatibility)
                        $quizzes = array_fill(0, 4, 0);
                        $assignments = array_fill(0, 4, 0);
                        $midterms = array_fill(0, 4, 0);
                        $finals = array_fill(0, 4, 0);

                        // Track indexes for backward-compatible arrays
                        $quizIndex = $assignmentIndex = $midIndex = $finalIndex = 0;

                        foreach ($assessments as $assessment) {
                            $marks = $assessment->obtained_marks ?? 10;
                            $clo = max(1, min(4, $assessment->clo_number ?? 1));
                            $title = strtolower($assessment->assessment->type ?? 'quiz');

                            // Store marks in CLO-specific arrays
                            if (stripos($title, 'quiz') !== false) {
                                $clos[$clo]['quizzes'] += $marks;
                                if ($quizIndex < 4) {
                                    $quizzes[$quizIndex++] = $marks;
                                }
                            } elseif (stripos($title, 'assignment') !== false) {
                                $clos[$clo]['assignments'] += $marks;
                                if ($assignmentIndex < 4) {
                                    $assignments[$assignmentIndex++] = $marks;
                                }
                            } elseif (stripos($title, 'mid') !== false) {
                                $clos[$clo]['midterms'] += $marks;
                                if ($midIndex < 4) {
                                    $midterms[$midIndex++] = $marks;
                                }
                            } else {
                                $clos[$clo]['finals'] += $marks;
                                if ($finalIndex < 4) {
                                    $finals[$finalIndex++] = $marks;
                                }
                            }
                        }

                        // Calculate averages and totals
                        $assignmentCount = count(array_filter($assignments, fn($v) => $v > 0));
                        $quizCount = count(array_filter($quizzes, fn($v) => $v > 0));
                        $totalAssignment = array_sum($assignments);
                        $totalQuiz = array_sum($quizzes);
                        $assignmentAvg = $assignmentCount > 0 ? ($totalAssignment / $assignmentCount) : 0;
                        $quizAvg = $quizCount > 0 ? ($totalQuiz / $quizCount) : 0;
                        $totalAQAvg = ($assignmentAvg + $quizAvg) / 2;

                        $totalMid = array_sum($midterms);
                        $totalFinal = array_sum($finals);
                        $grandTotal = $totalAQAvg + $totalMid + $totalFinal;

                        // Calculate CLO percentages
                        $cloPercentages = [];
                        foreach ($clos as $cloNumber => $marks) {
                            $obtained = $marks['quizzes'] + $marks['assignments'] + $marks['midterms'] + $marks['finals'];
                            $totalPossible = 15 + 10 + 5 + 10; // Adjust these values if needed
                            
                            $cloPercentage = $obtained > 0 ? ($obtained / $totalPossible) * 100 : 0;
                            $cloPercentages[$cloNumber] = round($cloPercentage, 2);
                        }

                        // Calculate PLOs using the CLO percentages
                        $plo1 = round(($cloPercentages[1] + $cloPercentages[2]) / 2, 1);
                        $plo2 = round(($cloPercentages[2] + $cloPercentages[3]) / 2, 1);
                        $plo3 = round(($cloPercentages[3] + $cloPercentages[4]) / 2, 1);

                        $finalTotalMarksRo_obtainmarks = array_merge(
                            $assignments,
                            $quizzes,
                            [$totalAQAvg],
                            $midterms,
                            [$totalMid],
                            $finals,
                            [$totalFinal]
                        );

                        $rowData = [
                            $sno++,
                            $reg_no,
                            $name,
                            ...$finalTotalMarksRo_obtainmarks,
                            round($grandTotal, 2),
                            $cloPercentages[1] ?? 0,
                            $cloPercentages[2] ?? 0,
                            $cloPercentages[3] ?? 0,
                            $cloPercentages[4] ?? 0,
                            '', // Blank column
                            $plo1,
                            $plo2,
                            $plo3
                        ];

                        $sheet->fromArray($rowData, null, 'A' . $rowIndex);

                        // Apply styling
                        $sheet->getStyle('D' . $rowIndex . ':G' . $rowIndex)->applyFromArray($assignmentColor);
                        $sheet->getStyle('H' . $rowIndex . ':K' . $rowIndex)->applyFromArray($quizColor);
                        $sheet->getStyle('M' . $rowIndex . ':P' . $rowIndex)->applyFromArray($MidColor);
                        $sheet->getStyle('R' . $rowIndex . ':U' . $rowIndex)->applyFromArray($finalColor);
                        $sheet->getStyle('W' . $rowIndex)->applyFromArray($FinaltotalColor);
                        $sheet->getStyle('AC' . $rowIndex . ':AE' . $rowIndex)->applyFromArray($PLO);
                        $rowIndex++;
                    }
                            // Center-align all content in the sheet
                    $sheet->getStyle($sheet->calculateWorksheetDimension())
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $sheet->getStyle($sheet->calculateWorksheetDimension())
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // You can use BORDER_MEDIUM for thicker border
                                'color' => ['argb' => 'FF000000'], // Solid Black
                            ],
                        ],
                    ]);
                    // Save as Excel (XLSX)
                    $filename = "OBE_report_" . now()->format('Y-m-d') . ".xlsx";
                    $writer = new Xlsx($spreadsheet);

                    return response()->streamDownload(
                    function () use ($writer) {
                        $writer->save('php://output');
                    },
                    $filename,
                    ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                    );
            } 


            public function exportToExcel_lab(Request $request) {
            // ... [Your existing data fetching code] ...

          

            $greenStyle = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF00FF00'] // Green
                ]
            ];
            $PLO = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'd4d9dc'] // Green
                ]
            ];
            $FinaltotalColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'b28c14'] // blue for assignment
                ]
            ];
            $assignmentColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'b8cce4'] // blue for assignment
                ]
            ];
            $quizColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'd7e4bc'] // Greenish  for quiz
                ]
            ];
            $MidColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'fac090'] // skin for Mid 
                ]
            ];
            $finalColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ccc0da'] // purpul for final 
                ]
            ];



            $CLO1Color = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ffff99'] // CLO1
                ]
            ];
            $CLO2Color = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ff65d7'] // CLO2
                ]
            ];
            $CLO3Color = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'b6dde8'] // CLO3
                ]
            ];
            $CLO4Color = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '99ff66'] // CLO4
                ]
            ];
             $herdercolor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '99ff66'] // CLO4
                ]
            ];
        
            $course_id = $request->course_id;
            $faculty_id = $request->faculty_id;

            // Fetch related data
            $assessments = Assessment::with(['marks', 'student' ,'studentdetail'])
                ->where('course_id', $course_id)
                ->where('teacher_id', $faculty_id)
                ->get();




            $studentsData = LabAssessmentRubricDetail::with(['assessment.student', 'assessment.studentdetail'])
            ->whereHas('assessment', function($query) use ($course_id, $faculty_id) {
                $query->where('course_id', $course_id)
                        ->where('teacher_id', $faculty_id);
            })
            ->get();

        //    dd($studentsData);
                $batchstudent = ($studentsData->first());
                $batch = $batchstudent->assessment->studentdetail->batch ?? 'empty';
                $section = $batchstudent->assessment->studentdetail->section ?? 'empty';

            $batchstudent = ($studentsData->first());
                
                // dd($assessments);
                $course = Course::find($course_id);
                $faculty = User::find($faculty_id);
                // dd($faculty);
                $courseName = $course->name ?? 'empty';
                $facultyName = $faculty->name ?? 'empty';
                $batch = $batchstudent->assessment->studentdetail->batch ?? 'empty';
                $section = $batchstudent->assessment->studentdetail->section ?? 'empty';
                // dd($section , $batch);
                // Group assessments by student
                $groupedAssessments = $assessments->groupBy('student_id');
            // Create a new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
        
            // Merge cells for Institute Name (Row 1)
            $sheet->mergeCells('A1:AD1'); // Merges 29 columns (A to AD)
            $sheet->setCellValue('A1', 'Foundation University Islamabad, Rawalpindi Campus');
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
            // Merge cells for Department (Row 2)
            $sheet->mergeCells('A2:AD2');
            $sheet->setCellValue('A2', 'Department of Engineering Technology');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
            // Merge cells for Course Info (Row 3)
            $sheet->mergeCells('A3:AD3');
            $sheet->setCellValue('A3', "Course: {$courseName} - Instructor: {$facultyName} - Class and Section: {$batch} - {$section} ");
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        

            
            $groupedStudents = [];

            $cloRow = [];
            foreach ($studentsData as $assessment) {
                    $student_id = $assessment->assessment->student_id ?? null;
                    if ($student_id) {
                        $groupedStudents[$student_id][] = $assessment;
                    }
            }
         


            $type = [];
            $colIndex = 4; // Starting from column D

            // $labTitles = [];
            // $midTitles = [];
            // $otherTitles = [];

            // foreach ($groupedStudents as $student_id => $assessments) {
            //     foreach ($assessments as $assessment) {
            //         $clo = $assessment->assessment->assessment_title ?? "lab";
            //         if ($clo) {
            //             // Categorize based on title
            //             if (stripos($clo, 'lab') !== false) {
            //                 $labTitles[] = $clo;
            //             } elseif (stripos($clo, 'mid') !== false) {
            //                 $midTitles[] = $clo;
            //             } else {
            //                 $otherTitles[] = $clo;
            //             }
            //         }
            //     }
            // }

            $labTitles = [];
            $midTitles = [];
            $otherTitles = [];

            // Process only the first student
            foreach ($groupedStudents as $student_id => $assessments) {
                foreach ($assessments as $assessment) {
                    $clo = $assessment->assessment->assessment_title ?? "lab";
                    if ($clo) {
                        // Categorize based on title
                        if (stripos($clo, 'lab') !== false) {
                            $labTitles[] = $clo;
                        } elseif (stripos($clo, 'mid') !== false) {
                            $midTitles[] = $clo;
                        } else {
                            $otherTitles[] = $clo;
                        }
                    }
                }
                // Only want first student, so break here
                break;
            }

            // dd($labTitles);

            // dd($labTitles);
            // Remove duplicates while preserving order
            // $labTitles = array_unique($labTitles);
            // $midTitles = array_unique($midTitles);
            // $otherTitles = array_unique($otherTitles);

            // Build final ordered list
            $type = array_merge(
                $labTitles,
                count($labTitles) ? ['Lab Total'] : [],
                $midTitles,
                count($midTitles) ? ['Mid Total'] : [],
                $otherTitles,
                ['Total'] , ['Grand Total'] ,  ['CLO1'] , ['CLO2'] , ['CLO3'] ,  ['CLO4'] , ['PLO1'] , ['PLO2'] , ['PLO3'] , ['PLO4'] 
            );

            // Now render the titles to Excel
            foreach ($type as $title) {
                $excelCol = Coordinate::stringFromColumnIndex($colIndex);

                // Adjust width for long titles
                if (in_array($title, ['Individual Project', 'Group Project'])) {
                    $sheet->getColumnDimension($excelCol)->setWidth(15);
                } else {
                    $sheet->getColumnDimension($excelCol)->setWidth(12);
                }

                // Optionally, set the header value in Excel
                $sheet->setCellValue($excelCol . '1', $title);

                $colIndex++;
            }

            $prefix = ['', '', '']; // A4, B4, C4
            $finalCloRow = array_merge($prefix, $type);
            $sheet->fromArray($finalCloRow, null, 'A4');

            // Merge A4:C4 and center the label
            $sheet->mergeCells('A4:C4');
            $sheet->setCellValue('A4', 'Lab #');
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);



            $sheet->getStyle('D4')->applyFromArray($CLO1Color);
            $sheet->getStyle('E4')->applyFromArray($CLO2Color);
            $sheet->getStyle('F4')->applyFromArray($CLO3Color);
            $sheet->getStyle('G4')->applyFromArray($CLO4Color);

            $sheet->getStyle('H4')->applyFromArray($CLO1Color); // Assignment CLO1
            $sheet->getStyle('I4')->applyFromArray($CLO2Color); // Assignment CLO1
            $sheet->getStyle('J4')->applyFromArray($CLO3Color); // Assignment CLO1
            $sheet->getStyle('K4')->applyFromArray($CLO4Color);
             // Assignment CLO1
            $sheet->getStyle('M4')->applyFromArray($CLO1Color); // Assignment CLO1
            $sheet->getStyle('N4')->applyFromArray($CLO2Color); // Assignment CLO1
            $sheet->getStyle('O4')->applyFromArray($CLO3Color); // Assignment CLO1
            $sheet->getStyle('P4')->applyFromArray($CLO4Color); // Assignment CLO1
            
            $sheet->getStyle('R4')->applyFromArray($CLO1Color); // Assignment CLO1
            $sheet->getStyle('S4')->applyFromArray($CLO2Color); // Assignment CLO1
            $sheet->getStyle('T4')->applyFromArray($CLO3Color); // Assignment CLO1
            $sheet->getStyle('U4')->applyFromArray($CLO4Color); // Assignment CLO1



            function colorBlockRangelab($sheet, $startCol, $endCol, $startRow, $endRow, $styleArray) {
                for ($row = $startRow; $row <= $endRow; $row++) {
                    $range = "{$startCol}{$row}:{$endCol}{$row}";
                    $sheet->getStyle($range)->applyFromArray($styleArray);
                }
            }

            

        $labCLOs = [];
        $midCLOs = [];
        $finalCLOs = [];
        // dd($groupedStudents);

        foreach ($groupedStudents as $student_id => $assessments) {
            foreach ($assessments as $assessment) {
                $clo = $assessment->clo_number ?? 'CLO-1';
                $title = $assessment->assessment->assessment_title ?? 'lab';

                // Classify CLO into the right group
                if (stripos($title, 'lab') !== false) {
                    $labCLOs[] = $clo;
                } elseif (stripos($title, 'mid') !== false) {
                    $midCLOs[] = $clo;
                } else {
                    $finalCLOs[] = $clo;
                }
            }
             // Only want first student, so break here
                break;
        }
        // dd($labCLOs);

        // Combine all CLOs with separators
        $finalCloRow = array_merge(
            ['', '', ''], // Columns A5, B5, C5
            $labCLOs,
            count($labCLOs) ? [''] : [],
            $midCLOs,
            count($midCLOs) ? [''] : [],
            $finalCLOs,
            count($finalCLOs) ? [''] : [],
        );

        // Set CLOs in Row 5
        $sheet->fromArray($finalCloRow, null, 'A5');

        // Merge and label for "CLO Number"
        $sheet->mergeCells('A5:C5');
        $sheet->setCellValue('A5', 'CLO Number');
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            


            $labRubrics = [];
            $midRubrics = [];
            $otherRubrics = [];

            // dd($assessment->rubric_number);
            foreach ($groupedStudents as $student_id => $assessments) {
                foreach ($assessments as $assessment) {
                    $rubric = $assessment->rubric_number ?? 'R1';
                    $title = $assessment->assessment->assessment_title ?? 'lab';

                    if (stripos($title, 'Lab') !== false) {
                        $labRubrics[] = $rubric;
                    } elseif (stripos($title, 'Mid') !== false) {
                        $midRubrics[] = $rubric;
                    } else {
                        $otherRubrics[] = $rubric;
                    }
                }
                 // Only want first student, so break here
                break;
            }
// dd($otherRubrics);

            // Build final rubric row with empty cell after each group
            $rubricRow = array_merge(
                ['', '', ''], // Placeholder for columns A6, B6, C6
                $labRubrics,
                count($labRubrics) ? [''] : [],   // Empty cell after lab group
                $midRubrics,
                count($midRubrics) ? [''] : [],   // Empty cell after mid group
                $otherRubrics,
                count($otherRubrics) ? [''] : [], // Empty cell after project/other group
            );

            // Write to Row 6
            $sheet->fromArray($rubricRow, null, 'A6');

            // Optional: Merge and label
            $sheet->mergeCells('A6:C6');
            $sheet->setCellValue('A6', 'Rubrics');
            $sheet->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        


        $labMarks = [];
        $midMarks = [];
        $finalMarks = [];

        foreach ($groupedStudents as $student_id => $assessments) {
            foreach ($assessments as $assessment) {
                $marks = $assessment->total_marks ?? 10;
                $title = $assessment->assessment->assessment_title ?? 'lab';

                if (stripos($title, 'lab') !== false) {
                    $labMarks[] = $marks;
                } elseif (stripos($title, 'mid') !== false) {
                    $midMarks[] = $marks;
                } else {
                    $finalMarks[] = $marks;
                }
            }
             // Only want first student, so break here
                break;
        }

        // Build the final row
        $finalTotalMarksRow = array_merge(
            ['', '', ''], // For columns A7, B7, C7
            $labMarks,
            count($labMarks) ? ['25'] : [],
            $midMarks,
            count($midMarks) ? ['25'] : [],
            $finalMarks,
            count($finalMarks) ? ['50'] : [],
        );

        // Set row 7 with Total Marks
        $sheet->fromArray($finalTotalMarksRow, null, 'A7');

        // Merge and label
        $sheet->mergeCells('A7:C7');
        $sheet->setCellValue('A7', 'Total Marks');
        $sheet->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
 

            $sheet->getColumnDimension('A')->setWidth(6);   // S.No.
            $sheet->getColumnDimension('B')->setWidth(30);  // Registration No
            $sheet->getColumnDimension('C')->setWidth(25);  // Name


       
            $rowIndex = 8;
                $sno = 1;

                foreach ($groupedStudents as $student_id => $assessments) {
                    $first = $assessments[0];
                    $student = $first->assessment->student ?? null;
                    $studentDetail = $first->assessment->studentdetail ?? null;

                    if (!$student || !$studentDetail) continue;

                    $reg_no = 'FUI/FURC/' . $studentDetail->batch . '-BSET-' . $studentDetail->roll_number;
                    $name = $student->name ?? 'N/A';

                    // Initialize variables
                    $row = [$sno++, $reg_no, $name];
                    $labMarks = [];
                    $midMarks = [];
                    $projectMarks = [];

                    // Categorize marks by assessment type
                    foreach ($assessments as $a) {
                        $title = strtolower($a->assessment->assessment_title ?? 'lab');
                        $marks = $a->obtained_marks ?? 0;

                        if (strpos($title, 'lab') !== false) {
                            $labMarks[] = $marks;
                        } elseif (strpos($title, 'mid') !== false) {
                            $midMarks[] = $marks;
                        } elseif (strpos($title, 'project') !== false) {
                            $projectMarks[] = $marks;
                        }
                    }

                    // Add lab marks to row
                    foreach ($labMarks as $mark) {
                        $row[] = $mark;
                    }
                    // Add lab total
                    $labTotal = count($labMarks) > 0 ? array_sum($labMarks) : 0;
                    $row[] = $labTotal;

                    // Add mid marks to row
                    foreach ($midMarks as $mark) {
                        $row[] = $mark;
                    }
                    // Add mid total
                    $midTotal = count($midMarks) > 0 ? array_sum($midMarks) : 0;
                    $row[] = $midTotal;

                    // Add project marks to row
                    foreach ($projectMarks as $mark) {
                        $row[] = $mark;
                    }
                    // Add project total
                    $projectTotal = count($projectMarks) ? array_sum($projectMarks) : 0;
                    $row[] = $projectTotal;

                    // Add grand total
                    $grandTotal = $labTotal + $midTotal + $projectTotal;
                    $row[] = $grandTotal;

                $cloDefaults = [
                    'CLO-1' => '',
                    'CLO-2' => '',
                    'CLO-3' => '',
                    'CLO-4' => ''
                ];

                // Calculate actual CLO averages
                $cloMarksMap = [];
                foreach ($assessments as $a) {
                    $clo = $a->clo_number ?? 'CLO-1';
                    $cloMarksMap[$clo][] = $a->obtained_marks ?? 0;
                }

                $cloAverages = $cloDefaults;
                foreach ($cloMarksMap as $clo => $marks) {
                    if (array_key_exists($clo, $cloDefaults)) {
                        $cloAverages[$clo] = count($marks) ? round(array_sum($marks)/count($marks), 2) : '';
                    }
                }

                    // Add CLO averages (CLO1-CLO4)
                    for ($i = 1; $i <= 4; $i++) {
                        $row[] = $cloAverages['CLO-'.$i] ?? '';
                    }

                // Calculate PLOs only when we have numeric values for both required CLOs
                $plo1 = (is_numeric($cloAverages['CLO-1']) && is_numeric($cloAverages['CLO-2']))
                    ? round(($cloAverages['CLO-1'] + $cloAverages['CLO-2'])/2, 2)
                    : null;

                $plo2 = (is_numeric($cloAverages['CLO-2']) && is_numeric($cloAverages['CLO-3']))
                    ? round(($cloAverages['CLO-2'] + $cloAverages['CLO-3'])/2, 2)
                    : null;

                $plo3 = (is_numeric($cloAverages['CLO-3']) && is_numeric($cloAverages['CLO-4']))
                    ? round(($cloAverages['CLO-3'] + $cloAverages['CLO-4'])/2, 2)
                    : null;

                $plo4 = (is_numeric($cloAverages['CLO-4']) && is_numeric($cloAverages['CLO-1']))
                    ? round(($cloAverages['CLO-4'] + $cloAverages['CLO-1'])/2, 2)
                    : null;

                    // Add PLOs to row
                    $row[] = $plo1;
                    $row[] = $plo2;
                    $row[] = $plo3;
                    $row[] = $plo4;

                    // Write to sheet
                    $sheet->fromArray($row, null, 'A'.$rowIndex);
                    $rowIndex++;
                }


            // Define header color style
            $headerColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'f5c07d']
                ]
            ];

            // Apply to first 7 rows, adjust the column range (e.g., A to Z or more if needed)
            $sheet->getStyle('A1:AD7')->applyFromArray($headerColor);


            
                    // Center-align all content in the sheet
            $sheet->getStyle($sheet->calculateWorksheetDimension())
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->getStyle($sheet->calculateWorksheetDimension())
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // You can use BORDER_MEDIUM for thicker border
                        'color' => ['argb' => 'FF000000'], // Solid Black
                    ],
                ],
            ]);
            // Save as Excel (XLSX)
            $filename = "OBE_report_lab" . now()->format('Y-m-d') . ".xlsx";
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
        }

         public function exportToExcel_lab_old(Request $request) {
            // ... [Your existing data fetching code] ...

          

            $greenStyle = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF00FF00'] // Green
                ]
            ];
            $PLO = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'd4d9dc'] // Green
                ]
            ];
            $FinaltotalColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'b28c14'] // blue for assignment
                ]
            ];
            $assignmentColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'b8cce4'] // blue for assignment
                ]
            ];
            $quizColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'd7e4bc'] // Greenish  for quiz
                ]
            ];
            $MidColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'fac090'] // skin for Mid 
                ]
            ];
            $finalColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ccc0da'] // purpul for final 
                ]
            ];



            $CLO1Color = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ffff99'] // CLO1
                ]
            ];
            $CLO2Color = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ff65d7'] // CLO2
                ]
            ];
            $CLO3Color = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'b6dde8'] // CLO3
                ]
            ];
            $CLO4Color = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '99ff66'] // CLO4
                ]
            ];
             $herdercolor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '99ff66'] // CLO4
                ]
            ];
        
            $course_id = $request->course_id;
            $faculty_id = $request->faculty_id;

            // Fetch related data
            $assessments = Assessment::with(['marks', 'student' ,'studentdetail'])
                ->where('course_id', $course_id)
                ->where('teacher_id', $faculty_id)
                ->get();




            $studentsData = LabAssessmentRubricDetail::with(['assessment.student', 'assessment.studentdetail'])
            ->whereHas('assessment', function($query) use ($course_id, $faculty_id) {
                $query->where('course_id', $course_id)
                        ->where('teacher_id', $faculty_id);
            })
            ->get();

        //    dd($studentsData);

            $batchstudent = ($studentsData->first());
                
                // dd($assessments);
                $course = Course::find($course_id);
                $faculty = User::find($faculty_id);
                // dd($faculty);
                $courseName = $course->name ?? 'empty';
                $facultyName = $faculty->name ?? 'empty';
                $batch = $batchstudent->assessment->studentdetail->batch ?? 'empty';
                $section = $batchstudent->assessment->studentdetail->section ?? 'empty';
                // dd($section , $batch);
                // Group assessments by student
                $groupedAssessments = $assessments->groupBy('student_id');
            // Create a new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
        
            // Merge cells for Institute Name (Row 1)
            $sheet->mergeCells('A1:AD1'); // Merges 29 columns (A to AD)
            $sheet->setCellValue('A1', 'Foundation University Islamabad, Rawalpindi Campus');
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
            // Merge cells for Department (Row 2)
            $sheet->mergeCells('A2:AD2');
            $sheet->setCellValue('A2', 'Department of Engineering Technology');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
            // Merge cells for Course Info (Row 3)
            $sheet->mergeCells('A3:AD3');
            $sheet->setCellValue('A3', "Course: {$courseName} - Instructor: {$facultyName} - Class and Section: {$batch} - {$section} ");
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        

            
            $groupedStudents = [];

            $cloRow = [];
            foreach ($studentsData as $assessment) {
                    $student_id = $assessment->assessment->student_id ?? null;
                    if ($student_id) {
                        $groupedStudents[$student_id][] = $assessment;
                    }
            }
         


            $type = [];
            $colIndex = 4; // Starting from column D

            $labTitles = [];
            $midTitles = [];
            $otherTitles = [];

            foreach ($groupedStudents as $student_id => $assessments) {
                foreach ($assessments as $assessment) {
                    $clo = $assessment->assessment->assessment_title ?? "lab";

                    // dd($clo);
                    if ($clo) {
                        // Categorize based on title
                        if (stripos($clo, 'lab') !== false) {
                            $labTitles[] = $clo;
                        } elseif (stripos($clo, 'mid') !== false) {
                            $midTitles[] = $clo;
                        } else {
                            $otherTitles[] = $clo;
                        }
                    }
                }
            }
            // dd($midTitles);

            // Remove duplicates while preserving order
            // $labTitles = array_unique($labTitles);
            // $midTitles = array_unique($midTitles);
            // $otherTitles = array_unique($otherTitles);

            // Build final ordered list
            $type = array_merge(
                $labTitles,
                count($labTitles) ? ['Lab Total'] : [],
                $midTitles,
                count($midTitles) ? ['Mid Total'] : [],
                $otherTitles,
                ['Total'] , ['Grand Total'] ,  [''] ,  ['CLO1'] , ['CLO2'] , ['CLO3'] ,  ['CLO4'] , ['PLO1'] , ['PLO2'] , ['PLO3'] , ['PLO4'] 
            );
            // dd($type);

            // Now render the titles to Excel
            foreach ($type as $title) {
                $excelCol = Coordinate::stringFromColumnIndex($colIndex);

                // Adjust width for long titles
                if (in_array($title, ['Individual Project', 'Group Project'])) {
                    $sheet->getColumnDimension($excelCol)->setWidth(15);
                } else {
                    $sheet->getColumnDimension($excelCol)->setWidth(12);
                }

                // Optionally, set the header value in Excel
                $sheet->setCellValue($excelCol . '1', $title);

                $colIndex++;
            }

            $prefix = ['', '', '']; // A4, B4, C4
            $finalCloRow = array_merge($prefix, $type);
            $sheet->fromArray($finalCloRow, null, 'A4');

            // Merge A4:C4 and center the label
            $sheet->mergeCells('A4:C4');
            $sheet->setCellValue('A4', 'Lab #');
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);



            $sheet->getStyle('D4')->applyFromArray($CLO1Color);
            $sheet->getStyle('E4')->applyFromArray($CLO2Color);
            $sheet->getStyle('F4')->applyFromArray($CLO3Color);
            $sheet->getStyle('G4')->applyFromArray($CLO4Color);

            $sheet->getStyle('H4')->applyFromArray($CLO1Color); // Assignment CLO1
            $sheet->getStyle('I4')->applyFromArray($CLO2Color); // Assignment CLO1
            $sheet->getStyle('J4')->applyFromArray($CLO3Color); // Assignment CLO1
            $sheet->getStyle('K4')->applyFromArray($CLO4Color);
             // Assignment CLO1
            $sheet->getStyle('M4')->applyFromArray($CLO1Color); // Assignment CLO1
            $sheet->getStyle('N4')->applyFromArray($CLO2Color); // Assignment CLO1
            $sheet->getStyle('O4')->applyFromArray($CLO3Color); // Assignment CLO1
            $sheet->getStyle('P4')->applyFromArray($CLO4Color); // Assignment CLO1
            
            $sheet->getStyle('R4')->applyFromArray($CLO1Color); // Assignment CLO1
            $sheet->getStyle('S4')->applyFromArray($CLO2Color); // Assignment CLO1
            $sheet->getStyle('T4')->applyFromArray($CLO3Color); // Assignment CLO1
            $sheet->getStyle('U4')->applyFromArray($CLO4Color); // Assignment CLO1



            function colorBlockRangelab($sheet, $startCol, $endCol, $startRow, $endRow, $styleArray) {
                for ($row = $startRow; $row <= $endRow; $row++) {
                    $range = "{$startCol}{$row}:{$endCol}{$row}";
                    $sheet->getStyle($range)->applyFromArray($styleArray);
                }
            }

            

        $labCLOs = [];
        $midCLOs = [];
        $finalCLOs = [];

        foreach ($groupedStudents as $student_id => $assessments) {
            foreach ($assessments as $assessment) {
                $clo = $assessment->clo_number ?? 'CLO-1';
                $title = $assessment->assessment->assessment_title ?? 'lab';

                // Classify CLO into the right group
                if (stripos($title, 'lab') !== false) {
                    $labCLOs[] = $clo;
                } elseif (stripos($title, 'mid') !== false) {
                    $midCLOs[] = $clo;
                } else {
                    $finalCLOs[] = $clo;
                }
            }
        }

        // Combine all CLOs with separators
        $finalCloRow = array_merge(
            ['', '', ''], // Columns A5, B5, C5
            $labCLOs,
            count($labCLOs) ? [''] : [],
            $midCLOs,
            count($midCLOs) ? [''] : [],
            $finalCLOs,
            count($finalCLOs) ? [''] : [],
        );

        // Set CLOs in Row 5
        $sheet->fromArray($finalCloRow, null, 'A5');

        // Merge and label for "CLO Number"
        $sheet->mergeCells('A5:C5');
        $sheet->setCellValue('A5', 'CLO Number');
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            


            $labRubrics = [];
            $midRubrics = [];
            $otherRubrics = [];

            // dd($assessment->rubric_number);
            foreach ($groupedStudents as $student_id => $assessments) {
                foreach ($assessments as $assessment) {
                    $rubric = $assessment->rubric_number ?? 'R1';
                    $title = $assessment->assessment->assessment_title ?? 'lab';

                    if (stripos($title, 'Lab') !== false) {
                        $labRubrics[] = $rubric;
                    } elseif (stripos($title, 'Mid') !== false) {
                        $midRubrics[] = $rubric;
                    } else {
                        $otherRubrics[] = $rubric;
                    }
                }
            }
// dd($otherRubrics);

            // Build final rubric row with empty cell after each group
            $rubricRow = array_merge(
                ['', '', ''], // Placeholder for columns A6, B6, C6
                $labRubrics,
                count($labRubrics) ? [''] : [],   // Empty cell after lab group
                $midRubrics,
                count($midRubrics) ? [''] : [],   // Empty cell after mid group
                $otherRubrics,
                count($otherRubrics) ? [''] : [], // Empty cell after project/other group
            );

            // Write to Row 6
            $sheet->fromArray($rubricRow, null, 'A6');

            // Optional: Merge and label
            $sheet->mergeCells('A6:C6');
            $sheet->setCellValue('A6', 'Rubrics');
            $sheet->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        


        $labMarks = [];
        $midMarks = [];
        $finalMarks = [];

        foreach ($groupedStudents as $student_id => $assessments) {
            foreach ($assessments as $assessment) {
                $marks = $assessment->total_marks ?? 10;
                $title = $assessment->assessment->assessment_title ?? 'lab';

                if (stripos($title, 'lab') !== false) {
                    $labMarks[] = $marks;
                } elseif (stripos($title, 'mid') !== false) {
                    $midMarks[] = $marks;
                } else {
                    $finalMarks[] = $marks;
                }
            }
        }

        // Build the final row
        $finalTotalMarksRow = array_merge(
            ['', '', ''], // For columns A7, B7, C7
            $labMarks,
            count($labMarks) ? ['25'] : [],
            $midMarks,
            count($midMarks) ? ['25'] : [],
            $finalMarks,
            count($finalMarks) ? ['50'] : [],
        );

        // Set row 7 with Total Marks
        $sheet->fromArray($finalTotalMarksRow, null, 'A7');

        // Merge and label
        $sheet->mergeCells('A7:C7');
        $sheet->setCellValue('A7', 'Total Marks');
        $sheet->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
 

            $sheet->getColumnDimension('A')->setWidth(6);   // S.No.
            $sheet->getColumnDimension('B')->setWidth(30);  // Registration No
            $sheet->getColumnDimension('C')->setWidth(25);  // Name


            $uniqueTitles = [];
            $rowIndex = 8;
            $sno = 1;

            foreach ($groupedStudents as $student_id => $assessments) {
                    $first = $assessments[0];
                    $student = $first->assessment->student ?? null;
                    $studentDetail = $first->assessment->studentdetail ?? null;

                    if (!$student || !$studentDetail) continue;

                    $reg_no = 'FUI/FURC/' . $studentDetail->batch . '-BSET-' . $studentDetail->roll_number;
                    $name = $student->name ?? 'N/A';

                    // Start of row
                    $row = [
                        $sno++,    // Column A: SNO
                        $reg_no,   // Column B: Registration Number
                        $name      // Column C: Name
                    ];

                    $marksMap = [];
                    $labTotal = 0;
                    $midTotal = 0;
                    $finalTotal = 0;
                    $otherTotal = 0;
                    $totallab = 0;

                    // Map obtained marks by title
                    foreach ($assessments as $a) {
                        $title = $a->assessment->assessment_title ?? 'lab';
                        $marksMap[$title][] = $a->obtained_marks ?? 0;
                    }

                    $labCount = 0;
                    foreach ($type as $t) {
                        if (stripos($t, 'lab') !== false) {
                            $labCount++;
                        }
                    }

                    // Fill row values in correct order
                $lastKey = array_key_last($type);

                foreach ($type as $index => $title) {
                    if ($title === 'Lab Total') {
                        $totallab = ($labCount > 1) ? round($labTotal / ($labCount - 1), 2) : 0;
                        $row[] = $totallab;
                    } elseif ($title === 'Mid Total') {
                        $row[] = $midTotal;
                    } elseif ($title === 'Total') {
                        $totalSum = $otherTotal;
                        $row[] = $totalSum;
                    } else {
                        $marks = $marksMap[$title] ?? [0];
                        $sum = array_sum($marks);
                        $row[] = $sum;

                        // Update grouped totals
                        if (stripos($title, 'lab') !== false) {
                            $labTotal += $sum;
                        } elseif (stripos($title, 'mid') !== false) {
                            $midTotal += $sum;
                        } else {
                            $otherTotal += $sum;
                        }
                    }
                }



                // ✅ Always add Grand Total at the very end
                    $grandTotal = $totallab + $midTotal + $otherTotal;

                    // Insert Grand Total before the last item in the row
                    array_splice($row, -10, 0, $grandTotal);

                $cloMarksMap = [];
                    foreach ($assessments as $a) {
                        $clo = $a->clo_number ?? 'CLO-1';
                        $obtained = $a->obtained_marks ?? 0;
                        $cloMarksMap[$clo][] = $obtained;
                    }

                    // Calculate averages
                    $cloAverages = [];
                    foreach ($cloMarksMap as $clo => $marks) {
                        $avg = count($marks) ? round(array_sum($marks) / count($marks), 2) : 0;
                        $cloAverages[$clo] = $avg;
                    }

                    // Insert averages into $row at position -9 (replace with correct index if needed)
                    array_splice($row, -9, 0, array_values($cloAverages));

                    // ✅ Calculate and add PLOs
                    $plo1 = round((($cloAverages['CLO-1'] ?? 0) + ($cloAverages['CLO-2'] ?? 0)) / 2, 2);
                    $plo2 = round((($cloAverages['CLO-2'] ?? 0) + ($cloAverages['CLO-3'] ?? 0)) / 2, 2);
                    $plo3 = round((($cloAverages['CLO-3'] ?? 0) + ($cloAverages['CLO-4'] ?? 0)) / 2, 2);
                    $plo4 = round((($cloAverages['CLO-4'] ?? 0) + ($cloAverages['CLO-1'] ?? 0)) / 2, 2);

                    array_splice($row, -9, 0, [$plo1, $plo2, $plo3, $plo4]);

                    // ✅ Write to sheet
                    $sheet->fromArray($row, null, 'A' . $rowIndex);
                    $rowIndex++;
            }


            // Define header color style
            $headerColor = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'f5c07d']
                ]
            ];

            // Apply to first 7 rows, adjust the column range (e.g., A to Z or more if needed)
            $sheet->getStyle('A1:AD7')->applyFromArray($headerColor);


            
                    // Center-align all content in the sheet
            $sheet->getStyle($sheet->calculateWorksheetDimension())
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->getStyle($sheet->calculateWorksheetDimension())
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // You can use BORDER_MEDIUM for thicker border
                        'color' => ['argb' => 'FF000000'], // Solid Black
                    ],
                ],
            ]);
            // Save as Excel (XLSX)
            $filename = "OBE_report_lab" . now()->format('Y-m-d') . ".xlsx";
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
        }



 public function exportToWord(Request $request)
        {
            $course_id = $request->course_id;
            $faculty_id = $request->faculty_id;

            $studentsData = assessment_clo_detail::with(['assessment.student', 'assessment.studentdetail','assessment.Cource'])
                ->whereHas('assessment', function ($query) use ($course_id, $faculty_id) {
                    $query->where('course_id', $course_id)
                        ->where('teacher_id', $faculty_id);
                })
                ->get();

            if ($studentsData->isEmpty()) {
                return redirect()->back()->with('error', 'No assessment data found for the selected course and faculty.');
            }

            $courcesdetail = $studentsData->first();
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            $headerStyle = ['alignment' => 'center', 'spaceAfter' => Converter::pointToTwip(12)];
            $boldStyle = ['bold' => true];
            $section->addText('Course Review Report', $boldStyle, $headerStyle);
            $section->addText('Course Title:' . $courcesdetail->assessment->Cource->name ?? 'N/A');
            $section->addText('Pre-requisite: ' . $courcesdetail->assessment->Cource->pre_req ?? 'N/A');
            $section->addText('No of Students Registered: ' . $studentsData->pluck('assessment.student_id')->unique()->count());
            $section->addTextBreak(1);

            $gradeCounts = [
                'A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0,
                'C+' => 0, 'C' => 0, 'D+' => 0, 'D' => 0, 'F' => 0
            ];

            $cloTotals = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $cloCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $notAchievedPLO = [];

            $groupedStudents = [];
            foreach ($studentsData as $assessment) {
                $student_id = $assessment->assessment->student_id ?? null;
                if ($student_id) {
                    $groupedStudents[$student_id][] = $assessment;
                }
            }

            foreach ($groupedStudents as $student_id => $assessments) {
                $firstAssessment = $assessments[0];
                $student = $firstAssessment->assessment->student ?? null;
                $studentdetail = $firstAssessment->assessment->studentdetail ?? null;
                if (!$student || !$studentdetail) continue;

                $assignments = array_fill(1, 4, 0);
                $quizzes     = array_fill(1, 4, 0);
                $midterms    = array_fill(1, 4, 0);
                $finals      = array_fill(1, 4, 0);

                foreach ($assessments as $assessment) {
                    $clo = $assessment->clo_number ?? "N/A";
                    $obtained = $assessment->obtained_marks ?? 0;
                    $type = strtolower($assessment->assessment->type ?? '');
                    $cloNumber = (int)str_replace('CLO', '', $clo);

                    switch ($type) {
                        case 'assignment':
                            if (isset($assignments[$cloNumber])) $assignments[$cloNumber] = $obtained;
                            break;
                        case 'quiz':
                            if (isset($quizzes[$cloNumber])) $quizzes[$cloNumber] = $obtained;
                            break;
                        case 'mid':
                            if (isset($midterms[$cloNumber])) $midterms[$cloNumber] = $obtained;
                            break;
                        case 'final':
                            if (isset($finals[$cloNumber])) $finals[$cloNumber] = $obtained;
                            break;
                    }
                }

                $assignmentCount = count(array_filter($assignments, fn($v) => $v > 0));
                    $quizCount       = count(array_filter($quizzes, fn($v) => $v > 0));

                    // Sum of obtained marks
                    $totalAssignment = array_sum($assignments);
                    $totalQuiz       = array_sum($quizzes);

                    // Average (%) calculation
                    $assignmentAvg = $assignmentCount > 0 ? ($totalAssignment / ($assignmentCount))  : 0;
                    $quizAvg       = $quizCount > 0 ? ($totalQuiz / ($quizCount)) : 0;

                    // Total AQ average (simple average of both)
                    $totalAQAvg    = $assignmentAvg + $quizAvg;

                    $totalMid        = array_sum($midterms);
                    $totalFinal      = array_sum($finals);

                    $grandTotal      = $totalAQAvg + $totalMid + $totalFinal;


                $clos = [];
                for ($i = 1; $i <= 4; $i++) {
                    $assignmentMarks = $assignments[$i] ?? 0;
                    $quizMarks       = $quizzes[$i] ?? 0;
                    $midMarks        = $midterms[$i] ?? 0;
                    $finalMarks      = $finals[$i] ?? 0;
                    $obtained = $assignmentMarks + $quizMarks + $midMarks + $finalMarks;
                    $totalPossible = 15 + 10 + 5 + 10;
                    $cloPercentage = $obtained > 0 ? ($obtained / $totalPossible) * 100 : 0;
                    $clos[$i] = round($cloPercentage, 2);
                    $cloTotals[$i] += $clos[$i];
                    $cloCounts[$i]++;
                }

                $plo1 = round(($clos[1] + $clos[2]) / 2, 1);
                $plo2 = round(($clos[2] + $clos[3]) / 2, 1);
                $plo3 = round(($clos[3] + $clos[4]) / 2, 1);

                // Fetch all PLO names for this course
                $ploNames = \App\Models\practical_outcome::where('course_id', $course_id)->pluck('PLO')->unique()->values()->toArray();
                if (empty($ploNames)) {
                    $ploNames = ['PLO-1', 'PLO-2', 'PLO-3', 'PLO-4']; // fallback
                }

                // Calculate PLOs dynamically
                $studentPLOs = [];
                for ($i = 0; $i < count($ploNames); $i++) {
                    // Map each PLO to two consecutive CLOs (wrap around if needed)
                    $cloA = $clos[$i + 1] ?? 0;
                    $cloB = $clos[$i + 2] ?? 0;
                    $studentPLOs[$ploNames[$i]] = round((($cloA + $cloB) / 2), 2);
                }

                // Collect failed PLOs for students who passed the course
                $failedPLOs = [];
                foreach ($studentPLOs as $ploName => $ploValue) {
                    if ($ploValue < 50) {
                        $failedPLOs[] = $ploName . ' (' . $ploValue . '%)'; // Show PLO name and percentage
                    }
                }
                if ($grandTotal >= 50 && !empty($failedPLOs)) {
                    $notAchievedPLO[] = [
                        'name' => $student->name,
                        'plos' => implode(', ', $failedPLOs)
                    ];
                }

                $grade = match (true) {
                    $grandTotal >= 85 => 'A+',
                    $grandTotal >= 80 => 'A',
                    $grandTotal >= 75 => 'B+',
                    $grandTotal >= 70 => 'B',
                    $grandTotal >= 65 => 'C+',
                    $grandTotal >= 60 => 'C',
                    $grandTotal >= 55 => 'D+',
                    $grandTotal >= 50 => 'D',
                    default => 'F'
                };

                $gradeCounts[$grade]++;
            }


            $section->addTextBreak(1);
            $section->addText('1) Grades Distribution:', $boldStyle);

            $gradeTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'alignment' => 'left'
            ]);

            // Add header row with grade labels
            $gradeTable->addRow();
            foreach (array_keys($gradeCounts) as $grade) {
                $gradeTable->addCell(1000)->addText($grade, $boldStyle);
            }

            // Add row with count values
            $gradeTable->addRow();
            foreach ($gradeCounts as $count) {
                $gradeTable->addCell(1000)->addText($count);
            }



            // Add CLOs Achievement
            $section->addTextBreak(1);
            $section->addText('2) CLOs Achievement:', $boldStyle);
            for ($i = 1; $i <= 4; $i++) {
                $avg = $cloCounts[$i] > 0 ? round($cloTotals[$i] / $cloCounts[$i], 2) : 0;
                $section->addText("CLO{$i}: {$avg}%");
            }
           

            // Add PLO issues
            $section->addTextBreak(1);
            $section->addText('3) List of Students that did not achieve a PLO but passed the course:', $boldStyle);
            
            // Add a table for failed students and their failed PLOs
            if (!empty($notAchievedPLO)) {
                $failedTable = $section->addTable([
                    'borderSize' => 6,
                    'borderColor' => '000000',
                    'alignment' => 'left',
                    'cellMargin' => 80
                ]);
                // Header row
                $failedTable->addRow();
                $failedTable->addCell(4000)->addText('Student Name', $boldStyle);
                $failedTable->addCell(4000)->addText('Failed PLO(s)', $boldStyle);
                // Data rows
                foreach ($notAchievedPLO as $item) {
                    $failedTable->addRow();
                    $failedTable->addCell(4000)->addText($item['name']);
                    $failedTable->addCell(4000)->addText($item['plos']);
                }
            } else {
                $section->addText('No students failed a PLO but passed the course.');
            }


            $section->addTextBreak(1);
            $section->addText('4) Instructor Comments:', ['bold' => true]);

            $commentTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'alignment' => 'left',
                'cellMargin' => 80
            ]);

            $bold = ['bold' => true];

            // Header row
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('S No.', $bold);
            $commentTable->addCell(4000)->addText('Question', $bold);
            $commentTable->addCell(8000)->addText('Comment', $bold);

            // Row 1
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('1.');
            $commentTable->addCell(4000)->addText("Comments on the achievement of CLO's");
            $commentTable->addCell(8000)->addText("All CLOs except CLO-2 have been successfully achieved.");

            // Row 2
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('2.');
            $commentTable->addCell(4000)->addText("Steps taken to address last years QEC observations");
            $commentTable->addCell(8000)->addText("CLOs added along with Complexity level for Mid/Final Paper Questions. Rubrics/reasons in marking added in best, average, worst assignment samples.");

            // Row 3
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('3.');
            $commentTable->addCell(4000)->addText("Preparedness of students for your class");
            $commentTable->addCell(8000)->addText("Students had some basic concepts required. But they lack Database concepts.");

            // Row 4
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('4.');
            $commentTable->addCell(4000)->addText("Engineering Design Problem");
            $commentTable->addCell(8000)->addText("Project of Cryptography Algorithms Implementation in Programming languages was assigned in Lab.");

            // Row 5
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('5.');
            $commentTable->addCell(4000)->addText("Any suggestion for improving the student learning process next time");
            $commentTable->addCell(8000)->addText("Database and Programming should be pre-requisite to this course.");



            //  $allPLOs = failPLO::with(['course', 'student', 'faculty'])
            // // ->where('student_id', $student_id)
            // ->orderBy('created_at', 'desc') // latest entries first
            // ->get();
            // dd($allPLOs);


            $latestPLOs = failPLO::with(['course', 'student', 'faculty'])
            ->where('course_id', $course_id)
                ->whereIn('id', function ($query) {
                    $query->selectRaw('MAX(id)') // Assumes `id` increases with newer records
                        ->from('failPLO') // Replace with your actual table name
                        ->groupBy('student_id');
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // dd($latestPLOs);

            // Assuming $latestPLOs contains your collection of student records
                $section->addTextBreak(1);
                $section->addText('5) Student PLO Failures:', ['bold' => true]);

                // Create the table for student PLO failures
                $studentTable = $section->addTable([
                    'borderSize' => 6,
                    'borderColor' => '000000',
                    'alignment' => 'left',
                    'cellMargin' => 80,
                ]);

                // Header Row
                $studentTable->addRow();
                $studentTable->addCell(5000)->addText('Student Name', ['bold' => true]);
                $studentTable->addCell(5000)->addText('Failed PLOs', ['bold' => true]);

                // Data Rows
                if ($latestPLOs->isEmpty()) {
                    // Add a row showing "No student" when collection is empty
                    $studentTable->addRow();
                    $studentTable->addCell(5000)->addText('No student', ['bold' => true]);
                    $studentTable->addCell(5000)->addText('No PLO failures');
                } else {
                    // Existing loop for when there are records
                    foreach ($latestPLOs as $record) {
                        $studentTable->addRow();
                        
                        // Student Name
                        $studentName = $record->student->name ?? 'N/A';
                        $studentTable->addCell(5000)->addText($studentName);
                        
                        // Failed PLOs (formatted)
                        $failedPLOs = json_decode($record->failPLOs, true);
                        $ploText = '';

                        if (is_array($failedPLOs)) {
                            $ploText = implode(', ', array_keys($failedPLOs));
                        } else {
                            preg_match_all('/PLO-\d+/', $record->failPLOs, $matches);
                            $ploText = implode(' ', $matches[0] ?? []);
                        }
                        
                        $studentTable->addCell(5000)->addText(trim($ploText));
                    }
                }

            

                
              $section->addTextBreak(2);
            $section->addText(
                'Instructor Signature: ________________                Instructor Name: ___________________',
                ['bold' => true],  // Added bold style
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START]
            );

            $section->addTextBreak(1);
            $section->addText('6) Departmental Review:', ['bold' => true]);

            $reviewTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'alignment' => 'left',
                'cellMargin' => 80,
            ]);

            $bold = ['bold' => true];

            // Header Row
            $reviewTable->addRow();
            $reviewTable->addCell(700)->addText('S No.', $bold);
            $reviewTable->addCell(5000)->addText('Review Point', $bold);
            $reviewTable->addCell(800)->addText('Yes', $bold);
            $reviewTable->addCell(800)->addText('No', $bold);
            $reviewTable->addCell(3000)->addText('Remarks', $bold);

            // Rows (1–9)
            $reviewPoints = [
                'Course coverage (CLOs > 90 %)',
                'CLO achievement Satisfactory',
                'Cohort Failure if any',
                'Students feedback Satisfactory (> 50%)',
                'CLO Clearly Define',
                'Appropriate CLO to PLO Mapping',
                'Assessment carried out at desired taxonomy level or not',
                'Observation of QEC, if any, addressed',
                'Comments regarding Problem based learning',
            ];

            foreach ($reviewPoints as $i => $point) {
                $reviewTable->addRow();
                $reviewTable->addCell(700)->addText(($i + 1) . '.');
                $reviewTable->addCell(5000)->addText($point);
                $reviewTable->addCell(800)->addText(''); // Yes (blank)
                $reviewTable->addCell(800)->addText(''); // No (blank)
                $reviewTable->addCell(3000)->addText(''); // Remarks (blank)
            }

        

           $section->addTextBreak(2);
            $section->addText(
                'Suggestion / Recommendation:',
                ['bold' => true],
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START] // Changed to LEFT alignment
            );

            $section->addTextBreak(2);
            $section->addText(
                'Sign: _____________________________',
                ['bold' => true],
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START] // Changed to LEFT alignment
            );

            $section->addTextBreak(2);
            $section->addText(
                'Name: ____________________________              Date: ____________________________',
                ['bold' => true],  // Added bold style
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START]
            );



            $filename = "course_review_" . date('Y-m-d') . ".docx";
            $tempFile = tempnam(sys_get_temp_dir(), 'word');
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        }


          public function exportToWordlab(Request $request)
        {
            $course_id = $request->course_id;
            $faculty_id = $request->faculty_id;

            $studentsData = LabAssessmentRubricDetail::with(['assessment.student', 'assessment.studentdetail','assessment.Cource'])
                ->whereHas('assessment', function ($query) use ($course_id, $faculty_id) {
                    $query->where('course_id', $course_id)
                        ->where('teacher_id', $faculty_id);
                })
                ->get();
                // dd($studentsData);

            if ($studentsData->isEmpty()) {
                return redirect()->back()->with('error', 'No assessment data found for the selected course and faculty.');
            }

            $courcesdetail = $studentsData->first();
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            $headerStyle = ['alignment' => 'center', 'spaceAfter' => Converter::pointToTwip(12)];
            $boldStyle = ['bold' => true];
            $section->addText('Course Review Report', $boldStyle, $headerStyle);
            $section->addText('Course Title:' . $courcesdetail->assessment->Cource->name ?? 'N/A');
            $section->addText('Pre-requisite: ' . $courcesdetail->assessment->Cource->pre_req ?? 'N/A');
            $section->addText('No of Students Registered: ' . $studentsData->pluck('assessment.student_id')->unique()->count());
            $section->addTextBreak(1);

            $gradeCounts = [
                'A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0,
                'C+' => 0, 'C' => 0, 'D+' => 0, 'D' => 0, 'F' => 0
            ];

            $cloTotals = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $cloCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $notAchievedPLO = [];

            $groupedStudents = [];
            foreach ($studentsData as $assessment) {
                $student_id = $assessment->assessment->student_id ?? null;
                if ($student_id) {
                    $groupedStudents[$student_id][] = $assessment;
                }
            }

            // dd($groupedStudents);
         foreach ($groupedStudents as $student_id => $assessments) {
            $firstAssessment = $assessments[0];
            $student = $firstAssessment->assessment->student ?? null;
            $studentdetail = $firstAssessment->assessment->studentdetail ?? null;
            if (!$student || !$studentdetail) continue;

            $labs     = array_fill(1, 4, 0);
            $midterms = array_fill(1, 4, 0);
            $finals   = array_fill(1, 4, 0);

            foreach ($assessments as $assessment) {
                
                $clo = $assessment->clo_number ?? "N/A";
                $obtained = $assessment->obtained_marks ?? 0;
                $title = strtolower($assessment->assessment->type ?? '');

                $cloNumber = (int) str_replace('CLO-', '', $clo);
                // dd($assessment);
                if (!isset($labs[$cloNumber])) continue;

                if (stripos($title, 'lab') !== false) {
                    $labs[$cloNumber] = $obtained;
                } elseif (stripos($title, 'mid') !== false) {
                    $midterms[$cloNumber] = $obtained;
                } else {
                    $finals[$cloNumber] = $obtained;
                }
            }

            // Count non-zero entries
            $labCount  = count(array_filter($labs, fn($v) => $v > 0));
            $midCount  = count(array_filter($midterms, fn($v) => $v > 0));
            $finalCount = count(array_filter($finals, fn($v) => $v > 0));

            // Totals
            $totalLab   = array_sum($labs);
            $totalMid   = array_sum($midterms);
            $totalFinal = array_sum($finals);

            // Simple average across types
            $labAvg     = $labCount > 0 ? ($totalLab / $labCount) : 0;

            // Calculate Grand Total
            $grandTotal = $labAvg + $totalMid + $totalFinal;

            // CLO-level % calculation
            for ($i = 1; $i <= 4; $i++) {
                $labMark    = $labs[$i] ?? 0;
                $midMark    = $midterms[$i] ?? 0;
                $finalMark  = $finals[$i] ?? 0;

                $obtained = $labMark + $midMark + $finalMark;
                $totalPossible = 10 + 10 + 10; // adjust this according to actual max marks per assessment
                $cloPercentage = $obtained > 0 ? ($obtained / $totalPossible) * 100 : 0;

                $clos[$i] = round($cloPercentage, 2);
                $cloTotals[$i] += $clos[$i];
                $cloCounts[$i]++;
            }

            $plo1 = round(($clos[1] + $clos[2]) / 2, 1);
            $plo2 = round(($clos[2] + $clos[3]) / 2, 1);
            $plo3 = round(($clos[3] + $clos[4]) / 2, 1);

            // Fetch all PLO names for this course
            $ploNames = \App\Models\practical_outcome::where('course_id', $course_id)->pluck('PLO')->unique()->values()->toArray();
            if (empty($ploNames)) {
                $ploNames = ['PLO-1', 'PLO-2', 'PLO-3', 'PLO-4']; // fallback
            }

            // Calculate PLOs dynamically
            $studentPLOs = [];
            for ($i = 0; $i < count($ploNames); $i++) {
                // Map each PLO to two consecutive CLOs (wrap around if needed)
                $cloA = $clos[$i + 1] ?? 0;
                $cloB = $clos[$i + 2] ?? 0;
                $studentPLOs[$ploNames[$i]] = round((($cloA + $cloB) / 2), 2);
            }

            // Collect failed PLOs for students who passed the course
            $failedPLOs = [];
            foreach ($studentPLOs as $ploName => $ploValue) {
                if ($ploValue < 50) {
                    $failedPLOs[] = $ploName . ' (' . $ploValue . '%)'; // Show PLO name and percentage
                }
            }
            if ($grandTotal >= 50 && !empty($failedPLOs)) {
                $notAchievedPLO[] = [
                    'name' => $student->name,
                    'plos' => implode(', ', $failedPLOs)
                ];
            }

            $grade = match (true) {
                $grandTotal >= 85 => 'A+',
                $grandTotal >= 80 => 'A',
                $grandTotal >= 75 => 'B+',
                $grandTotal >= 70 => 'B',
                $grandTotal >= 65 => 'C+',
                $grandTotal >= 60 => 'C',
                $grandTotal >= 55 => 'D+',
                $grandTotal >= 50 => 'D',
                default => 'F'
            };

            $gradeCounts[$grade]++;
        }


            $section->addTextBreak(1);
            $section->addText('1) Grades Distribution:', $boldStyle);

            $gradeTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'alignment' => 'left'
            ]);

            // Add header row with grade labels
            $gradeTable->addRow();
            foreach (array_keys($gradeCounts) as $grade) {
                $gradeTable->addCell(1000)->addText($grade, $boldStyle);
            }

            // Add row with count values
            $gradeTable->addRow();
            foreach ($gradeCounts as $count) {
                $gradeTable->addCell(1000)->addText($count);
            }



            // Add CLOs Achievement
            $section->addTextBreak(1);
            $section->addText('2) CLOs Achievement:', $boldStyle);
            for ($i = 1; $i <= 4; $i++) {
                $avg = $cloCounts[$i] > 0 ? round($cloTotals[$i] / $cloCounts[$i], 2) : 0;
                $section->addText("CLO{$i}: {$avg}%");
            }
            //  dd($cloTotals);

            // Add PLO issues
            $section->addTextBreak(1);
            $section->addText('3) List of Students that did not achieve a PLO but passed the course:', $boldStyle);
            
            // Add a table for failed students and their failed PLOs
            if (!empty($notAchievedPLO)) {
                $failedTable = $section->addTable([
                    'borderSize' => 6,
                    'borderColor' => '000000',
                    'alignment' => 'left',
                    'cellMargin' => 80
                ]);
                // Header row
                $failedTable->addRow();
                $failedTable->addCell(4000)->addText('Student Name', $boldStyle);
                $failedTable->addCell(4000)->addText('Failed PLO(s)', $boldStyle);
                // Data rows
                foreach ($notAchievedPLO as $item) {
                    $failedTable->addRow();
                    $failedTable->addCell(4000)->addText($item['name']);
                    $failedTable->addCell(4000)->addText($item['plos']);
                }
            } else {
                $section->addText('No students failed a PLO but passed the course.');
            }


            $section->addTextBreak(1);
            $section->addText('4) Instructor Comments:', ['bold' => true]);

            $commentTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'alignment' => 'left',
                'cellMargin' => 80
            ]);

            $bold = ['bold' => true];

            // Header row
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('S No.', $bold);
            $commentTable->addCell(4000)->addText('Question', $bold);
            $commentTable->addCell(8000)->addText('Comment', $bold);

            // Row 1
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('1.');
            $commentTable->addCell(4000)->addText("Comments on the achievement of CLO's");
            $commentTable->addCell(8000)->addText("All CLOs except CLO-2 have been successfully achieved.");

            // Row 2
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('2.');
            $commentTable->addCell(4000)->addText("Steps taken to address last years QEC observations");
            $commentTable->addCell(8000)->addText("CLOs added along with Complexity level for Mid/Final Paper Questions. Rubrics/reasons in marking added in best, average, worst assignment samples.");

            // Row 3
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('3.');
            $commentTable->addCell(4000)->addText("Preparedness of students for your class");
            $commentTable->addCell(8000)->addText("Students had some basic concepts required. But they lack Database concepts.");

            // Row 4
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('4.');
            $commentTable->addCell(4000)->addText("Engineering Design Problem");
            $commentTable->addCell(8000)->addText("Project of Cryptography Algorithms Implementation in Programming languages was assigned in Lab.");

            // Row 5
            $commentTable->addRow();
            $commentTable->addCell(1000)->addText('5.');
            $commentTable->addCell(4000)->addText("Any suggestion for improving the student learning process next time");
            $commentTable->addCell(8000)->addText("Database and Programming should be pre-requisite to this course.");



            //  $allPLOs = failPLO::with(['course', 'student', 'faculty'])
            // // ->where('student_id', $student_id)
            // ->orderBy('created_at', 'desc') // latest entries first
            // ->get();
            // dd($allPLOs);


            $latestPLOs = failPLO::with(['course', 'student', 'faculty'])
                ->where('course_id', $course_id)
                ->whereIn('id', function ($query) {
                    $query->selectRaw('MAX(id)') // Assumes `id` increases with newer records
                        ->from('failPLO') // Replace with your actual table name
                        ->groupBy('student_id');
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // dd($latestPLOs);

            // Assuming $latestPLOs contains your collection of student records
                $section->addTextBreak(1);
                $section->addText('5) Student PLO Failures:', ['bold' => true]);

                // Create the table for student PLO failures
                $studentTable = $section->addTable([
                    'borderSize' => 6,
                    'borderColor' => '000000',
                    'alignment' => 'left',
                    'cellMargin' => 80,
                ]);

                // Header Row
                $studentTable->addRow();
                $studentTable->addCell(5000)->addText('Student Name', ['bold' => true]);
                $studentTable->addCell(5000)->addText('Failed PLOs', ['bold' => true]);

                // Data Rows
                if ($latestPLOs->isEmpty()) {
                    // Add a row showing "No student" when collection is empty
                    $studentTable->addRow();
                    $studentTable->addCell(5000)->addText('No student', ['bold' => true]);
                    $studentTable->addCell(5000)->addText('No PLO failures');
                } else {
                    // Existing loop for when there are records
                    foreach ($latestPLOs as $record) {
                        $studentTable->addRow();
                        
                        // Student Name
                        $studentName = $record->student->name ?? 'N/A';
                        $studentTable->addCell(5000)->addText($studentName);
                        
                        // Failed PLOs (formatted)
                        $failedPLOs = json_decode($record->failPLOs, true);
                        $ploText = '';

                        if (is_array($failedPLOs)) {
                            $ploText = implode(', ', array_keys($failedPLOs));
                        } else {
                            preg_match_all('/PLO-\d+/', $record->failPLOs, $matches);
                            $ploText = implode(' ', $matches[0] ?? []);
                        }
                        
                        $studentTable->addCell(5000)->addText(trim($ploText));
                    }
                }

            

                
              $section->addTextBreak(2);
            $section->addText(
                'Instructor Signature: ________________                Instructor Name: ___________________',
                ['bold' => true],  // Added bold style
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START]
            );

            $section->addTextBreak(1);
            $section->addText('6) Departmental Review:', ['bold' => true]);

            $reviewTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'alignment' => 'left',
                'cellMargin' => 80,
            ]);

            $bold = ['bold' => true];

            // Header Row
            $reviewTable->addRow();
            $reviewTable->addCell(700)->addText('S No.', $bold);
            $reviewTable->addCell(5000)->addText('Review Point', $bold);
            $reviewTable->addCell(800)->addText('Yes', $bold);
            $reviewTable->addCell(800)->addText('No', $bold);
            $reviewTable->addCell(3000)->addText('Remarks', $bold);

            // Rows (1–9)
            $reviewPoints = [
                'Course coverage (CLOs > 90 %)',
                'CLO achievement Satisfactory',
                'Cohort Failure if any',
                'Students feedback Satisfactory (> 50%)',
                'CLO Clearly Define',
                'Appropriate CLO to PLO Mapping',
                'Assessment carried out at desired taxonomy level or not',
                'Observation of QEC, if any, addressed',
                'Comments regarding Problem based learning',
            ];

            foreach ($reviewPoints as $i => $point) {
                $reviewTable->addRow();
                $reviewTable->addCell(700)->addText(($i + 1) . '.');
                $reviewTable->addCell(5000)->addText($point);
                $reviewTable->addCell(800)->addText(''); // Yes (blank)
                $reviewTable->addCell(800)->addText(''); // No (blank)
                $reviewTable->addCell(3000)->addText(''); // Remarks (blank)
            }

        

           $section->addTextBreak(2);
            $section->addText(
                'Suggestion / Recommendation:',
                ['bold' => true],
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START] // Changed to LEFT alignment
            );

            $section->addTextBreak(2);
            $section->addText(
                'Sign: _____________________________',
                ['bold' => true],
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START] // Changed to LEFT alignment
            );

            $section->addTextBreak(2);
            $section->addText(
                'Name: ____________________________              Date: ____________________________',
                ['bold' => true],  // Added bold style
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START]
            );



            $filename = "course_review_" . date('Y-m-d') . ".docx";
            $tempFile = tempnam(sys_get_temp_dir(), 'word');
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        }

        // public function plo_counseling_result(){
        /*public function plo_counseling_result(Request $request){

            $course_id = $request->course_id;
            $faculty_id = $request->faculty_id;

            // Get all students' assessment data
            // $course_id = '40';
            // $faculty_id = '12';

            $studentsData = LabAssessmentRubricDetail::with(['assessment.student', 'assessment.studentdetail'])
            ->whereHas('assessment', function($query) use ($course_id, $faculty_id) {
                $query->where('course_id', $course_id)
                        ->where('teacher_id', $faculty_id);
            })
            ->get();

                // dd($studentsData);
            if ($studentsData->isEmpty()) {
                return redirect()->back()->with('error', 'No assessment data found for the selected course and faculty.');
            }
            $groupedStudents = [];

            $cloRow = [];
            foreach ($studentsData as $assessment) {
                    $student_id = $assessment->assessment->student_id ?? null;
                    if ($student_id) {
                        $groupedStudents[$student_id][] = $assessment;
                    }
            }
         


            $cloPloData = [];

            foreach ($groupedStudents as $student_id => $assessments) {
                $cloObtained = [];
                $cloTotal = [];

                // Collect total and obtained marks per CLO
                foreach ($assessments as $a) {
                    $clo = $a->clo_number ?? 'CLO-1';
                    $obtained = $a->obtained_marks ?? 0;
                    $total = $a->total_marks ?? 10;

                    $cloObtained[$clo][] = $obtained;
                    $cloTotal[$clo][] = $total;
                }

                // Calculate CLO percentages
                $cloPercentages = [];
                foreach ($cloObtained as $clo => $obtainedMarks) {
                    $totalObtained = array_sum($obtainedMarks);
                    $totalMarks = array_sum($cloTotal[$clo] ?? [10]);

                    $percentage = $totalMarks > 0 ? round(($totalObtained / $totalMarks) * 100, 2) : 0;
                    $cloPercentages[$clo] = $percentage;
                }

                
                // $practical_outcomes = practical_outcome::where('course_id' , $course_id)->get();
                // Calculate PLOs based on CLO percentages
                $plo1 = round((($cloPercentages['CLO-1'] ?? 0) + ($cloPercentages['CLO-2'] ?? 0)) / 2, 2);
                $plo2 = round((($cloPercentages['CLO-2'] ?? 0) + ($cloPercentages['CLO-3'] ?? 0)) / 2, 2);
                $plo3 = round((($cloPercentages['CLO-3'] ?? 0) + ($cloPercentages['CLO-4'] ?? 0)) / 2, 2);
                $plo4 = round((($cloPercentages['CLO-4'] ?? 0) + ($cloPercentages['CLO-1'] ?? 0)) / 2, 2);

                // // Final data for each student
                // $cloPloData[$student_id] = [
                //     'clos' => $cloPercentages,
                //     'plos' => [
                //         'PLO-1' => $plo1,
                //         'PLO-2' => $plo2,
                //         'PLO-3' => $plo3,
                //         'PLO-4' => $plo4,
                //     ],
                // ];

               $plos = [];
                $course = '40';
                // PLO calculations (keep this logic as-is)
                $plo1 = round((($cloPercentages['CLO-1'] ?? 0) + ($cloPercentages['CLO-2'] ?? 0)) / 2, 2);
                $plo2 = round((($cloPercentages['CLO-2'] ?? 0) + ($cloPercentages['CLO-3'] ?? 0)) / 2, 2);
                $plo3 = round((($cloPercentages['CLO-3'] ?? 0) + ($cloPercentages['CLO-4'] ?? 0)) / 2, 2);
                $plo4 = round((($cloPercentages['CLO-4'] ?? 0) + ($cloPercentages['CLO-1'] ?? 0)) / 2, 2);

                // Fetch PLO names from table
                $practical_outcomes = practical_outcome::where('course_id', $course_id)->pluck('PLO')->toArray();

                if (!empty($practical_outcomes)) {
                    // Use only first 4 PLO names if more than 4
                    $ploNames = array_slice($practical_outcomes, 0, 4);
                } else {
                    // Fallback to default names
                    $ploNames = ['PLO-1', 'PLO-2', 'PLO-3', 'PLO-4'];
                }

                // Map values to names
                $ploValues = [$plo1, $plo2, $plo3, $plo4];
                foreach ($ploNames as $index => $ploName) {
                    $plos[$ploName] = $ploValues[$index] ?? 0;
                }

                // Final structure
                $cloPloData[$student_id] = [
                    'clos' => $cloPercentages,
                    'plos' => $plos,
                ];
            }


            // You can now return $cloPloData and use it in your dropdown
            // dd($cloPloData);


            
            $failPloData = []; // New array to store failing PLOs

            foreach ($cloPloData as $studentId => $data) {
                $failPLOs = [];

                foreach ($data['plos'] as $ploName => $ploValue) {
                    if ($ploValue < 50) {
                        $failPLOs[$ploName] = $ploValue;
                    }
                }

                // Only add if the student has failing PLOs
                if (!empty($failPLOs)) {
                    $failPloData[$studentId] = $failPLOs;
                }
            }
            // dd($failPloData);
            // return $cloPloData;

           // Get next semester
            $course = Course::where('id', $course_id)->first();
            $semester = $course->semester;

            // Get all courses in the next semester
            $All_courses = Course::where('semester', ($semester + 1))->get();

            // Store recommended course for each student
            $nextOfferplosCourse = [];

            foreach ($failPloData as $studentId => $failedPLOs) {
                $foundCourse = null;

                foreach ($All_courses as $course) {
                    $coursePLOs = practical_outcome::where('course_id', $course->id)->pluck('PLO')->toArray();

                    if (!empty(array_intersect($failedPLOs, $coursePLOs))) {
                        $foundCourse = $course->name; // first matching course
                        break;
                    }
                }

                // Assign either the first matched course or 'No Course'
                $nextOfferplosCourse[$studentId] = $foundCourse ?? 'No Course';
            }
            // dd($nextOfferplosCourse);

            foreach ($failPloData as $studentId => $failPLOs) {
                failPLO::create([
                    'course_id' => $course_id,
                    'student_id' => $studentId,
                    'faculty_id' => $faculty_id,
                    'failPLOs' => json_encode($failPLOs),
                    'nextOfferplosCource' => $nextOfferplosCourse,
                ]);
            }
                // dd($failPloData);
                
            return redirect()->back()->with('success', 'Failing PLOs uploaded successfully.');
        }*/











        public function plo__not_lab(Request $request){
            $course_id = $request->course_id;
            $faculty_id = $request->faculty_id;

            $studentsData = assessment_clo_detail::with(['assessment.student', 'assessment.studentdetail'])
            ->whereHas('assessment', function($query) use ($course_id, $faculty_id) {
                $query->where('course_id', $course_id)
                        ->where('teacher_id', $faculty_id);
            })
            ->get();
           
            if ($studentsData->isEmpty()) {
                return redirect()->back()->with('error', 'No assessment data found for the selected course and faculty.');
            }

            $groupedStudents = [];
            foreach ($studentsData as $assessment) {
                    $student_id = $assessment->assessment->student_id ?? null;
                    if ($student_id) {
                        $groupedStudents[$student_id][] = $assessment;
                    }
            }
         
            $cloPloData = [];
            foreach ($groupedStudents as $student_id => $assessments) {
                $cloObtained = [];
                $cloTotal = [];

                // Collect total and obtained marks per CLO
                foreach ($assessments as $a) {
                    $clo = $a->clo_number ?? 'CLO-1';
                    $obtained = $a->obtained_marks ?? 0;
                    $total = $a->total_marks ?? 10;

                    $cloObtained[$clo][] = $obtained;
                    $cloTotal[$clo][] = $total;
                }

                // Calculate CLO percentages
                $cloPercentages = [];
                foreach ($cloObtained as $clo => $obtainedMarks) {
                    $totalObtained = array_sum($obtainedMarks);
                    $totalMarks = array_sum($cloTotal[$clo] ?? [10]);

                    $percentage = $totalMarks > 0 ? round(($totalObtained / $totalMarks) * 100, 2) : 0;
                    $cloPercentages[$clo] = $percentage;
                }

                // Fetch PLO names from table
                $practical_outcomes = practical_outcome::where('course_id', $course_id)->pluck('PLO')->toArray();
                
                // Use actual PLO names from database or fallback to defaults
                $ploNames = !empty($practical_outcomes) ? $practical_outcomes : ['PLO-1', 'PLO-2', 'PLO-3', 'PLO-4'];
                
                // Calculate PLOs based on CLO percentages
               $plos = [];
                $cloCount = count($cloPercentages);
                
                if ($cloCount > 0) {
                    // Map CLOs to PLOs based on available data
                    foreach ($ploNames as $index => $ploName) {
                        $cloIndex1 = $index % $cloCount;
                        $cloIndex2 = ($index + 1) % $cloCount;
                        
                        $clo1 = 'CLO-' . ($cloIndex1 + 1);
                        $clo2 = 'CLO-' . ($cloIndex2 + 1);
                        
                        $ploValue = round((($cloPercentages[$clo1] ?? 0) + ($cloPercentages[$clo2] ?? 0)) / 2, 2);
                        $plos[$ploName] = $ploValue;
                    }
                }

                $cloPloData[$student_id] = [
                    'clos' => $cloPercentages,
                    'plos' => $plos,
                ];
            }

            // Process failing PLOs
            $failPloData = [];
            foreach ($cloPloData as $studentId => $data) {
                $failPLOs = [];
                foreach ($data['plos'] as $ploName => $ploValue) {
                    if ($ploValue < 50) {
                        $failPLOs[$ploName] = $ploValue;
                    }
                }
                if (!empty($failPLOs)) {
                    $failPloData[$studentId] = $failPLOs;
                }
            }

            // Get next semester courses
            $course = Course::where('id', $course_id)->first();
            $semester = $course->semester;
            $All_courses = Course::where('semester', ($semester + 1))->get();

            // Store recommended course for each student
            $nextOfferplosCourse = [];
            foreach ($failPloData as $studentId => $failedPLOs) {
                $foundCourse = null;
                foreach ($All_courses as $course) {
                    $coursePLOs = practical_outcome::where('course_id', $course->id)->pluck('PLO')->toArray();
                    if (!empty(array_intersect(array_keys($failedPLOs), $coursePLOs))) {
                        $foundCourse = $course->name;
                        break;
                    }
                }
                $nextOfferplosCourse[$studentId] = $foundCourse ?? 'No Course';
            }

            // Store failing PLOs in database
            foreach ($failPloData as $studentId => $failPLOs) {
                failPLO::create([
                    'course_id' => $course_id,
                    'student_id' => $studentId,
                    'faculty_id' => $faculty_id,
                    'failPLOs' => json_encode($failPLOs),
                    'nextOfferplosCource' => $nextOfferplosCourse[$studentId] ?? 'No Course',
                ]);
            }

            return redirect()->back()->with('success', 'Failing PLOs uploaded successfully.');
        }

        public function failed_plos_result(Request $request){
            // Check if user is QEC
            if (auth()->user()->role_id === 4) { // Assuming 4 is QEC role ID
                $courses = Course::all();
                $allPLOs = failPLO::with(['course', 'student', 'faculty'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                $latestPLOs = $allPLOs->unique('course_id')->values();
                return view('Qualityenhancementcell.failplos', compact('latestPLOs', 'courses'));
            }
            
            // Student view logic
            $user = Auth::user()->student;
            $coreuser = Auth::user();
            $student_id = $request->id;
            
            $courses = CourseAllocation::with(['faculty.user', 'course'])
                ->where('batch', $user->batch)
                ->where('section', $user->section)
                ->get();

            $courseRegistration = CourseRegistration::with([
                'student',
                'teacher',
                'course',
                'CourseAllocation.faculty',
                'courseAllocation.Course'
            ])->where('student_id', $coreuser->id)->get();

            $student_id = $request->id;

            $allPLOs = failPLO::with(['course', 'student', 'faculty'])
                ->where('student_id', $student_id)
                ->orderBy('created_at', 'desc')
                ->get();

            $latestPLOs = $allPLOs->unique('course_id')->values();
            return view('student.failplos', compact('latestPLOs', 'courses', 'user', 'coreuser', 'courseRegistration'));   
        }

    public function viewPLOs()
    {
        $user = Auth::user();
        $faculty = Faculty::where('user_id', $user->id)->first();
        $designation = $faculty->designation ?? 'HOD';
        $duties = \App\Models\Role::whereIn('id', json_decode($faculty->duties))->get();
        $plos = [
            [
                'title' => 'Engineering Knowledge',
                'desc' => 'An ability to apply knowledge of mathematics, science, engineering fundamentals, and an engineering specialization to the solution of complex engineering problems.'
            ],
            [
                'title' => 'Problem Analysis',
                'desc' => 'An ability to identify, formulate, research literature, and analyze complex engineering problems reaching substantiated conclusions using first principles of mathematics, natural sciences, and engineering sciences.'
            ],
            [
                'title' => 'Design/Development of Solutions',
                'desc' => 'An ability to design solutions for complex mechanical engineering problems and design systems, components, or processes that meet specified needs with appropriate consideration for public health and safety, cultural, societal, and environmental considerations.'
            ],
            [
                'title' => 'Investigation',
                'desc' => 'An ability to investigate complex engineering problems in a methodical way including literature survey, design and conduct of experiments, analysis and interpretation of experimental data, and synthesis of information to derive valid conclusions.'
            ],
            [
                'title' => 'Modern Tool Usage',
                'desc' => 'An ability to create, select, and apply appropriate techniques, resources, and modern engineering and IT tools, including prediction and modeling, to complex engineering activities, with an understanding of the limitations.'
            ],
            [
                'title' => 'The Engineer and Society',
                'desc' => 'An ability to apply reasoning informed by contextual knowledge to assess societal, health, safety, legal, and cultural issues and the consequent responsibilities relevant to professional engineering practice and solutions to complex engineering problems.'
            ],
            [
                'title' => 'Environment and Sustainability',
                'desc' => 'An ability to understand the impact of professional engineering solutions in societal and environmental contexts and demonstrate knowledge of, and need for sustainable development.'
            ],
            [
                'title' => 'Ethics',
                'desc' => 'Apply ethical principles and commit to professional ethics and responsibilities and norms of engineering practice.'
            ],
            [
                'title' => 'Individual and Teamwork',
                'desc' => 'An ability to work effectively, as an individual or in a team, on multifaceted and /or multidisciplinary settings.'
            ],
            [
                'title' => 'Communication',
                'desc' => 'An ability to communicate effectively, orally as well as in writing, on complex engineering activities with the engineering community and with society at large, such as being able to comprehend and write effective reports and design documentation, make effective presentations, and give and receive clear instructions.'
            ],
            [
                'title' => 'Project Management',
                'desc' => 'An ability to demonstrate management skills and apply engineering principles to ones own work, as a member and/or leader in a team, to manage projects in a multidisciplinary environment.'
            ],
            [
                'title' => 'Lifelong Learning',
                'desc' => 'An ability to recognize the need for, and have the preparation and ability to engage in, independent and life-long learning in the broadest context of technological change.'
            ],
        ];
        return view('lecturar.hod.plos', compact('plos', 'designation', 'duties'));
    }

    public function findCRR(Request $request)
    {
        try {
            $ploId = $request->plo_id;
            $courseId = $request->course_id;
            $selectedCourseId = $request->selected_course;

            // Get the failed PLOs
            $failPLO = failPLO::with(['course', 'student'])->find($ploId);
            if (!$failPLO) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed PLO record not found.'
                ]);
            }

            // Get the selected course
            $selectedCourse = Course::find($selectedCourseId);
            if (!$selectedCourse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected course not found.'
                ]);
            }

            // Get PLOs for both courses
            $originalPLOs = practical_outcome::where('course_id', $courseId)->pluck('PLO')->toArray();
            $selectedPLOs = practical_outcome::where('course_id', $selectedCourseId)->pluck('PLO')->toArray();

            // Find matching PLOs
            $matchingPLOs = array_intersect($originalPLOs, $selectedPLOs);

            if (empty($matchingPLOs)) {
                return response()->json([
                    'success' => true,
                    'message' => '<div class="alert alert-warning">No matching PLOs found between the courses. This course may not be suitable for improving the failed PLOs.</div>'
                ]);
            }

            // Get student's performance in the selected course if they've taken it
            $studentPerformance = CourseRegistration::where('student_id', $failPLO->student_id)
                ->where('course_id', $selectedCourseId)
                ->first();

            $message = '<div class="alert alert-info">';
            $message .= '<h5>CRR Analysis Results:</h5>';
            $message .= '<p><strong>Matching PLOs:</strong> ' . implode(', ', $matchingPLOs) . '</p>';
            
            if ($studentPerformance) {
                $message .= '<p><strong>Previous Performance:</strong> ';
                if ($studentPerformance->status === 'approved') {
                    $message .= 'Successfully completed this course.</p>';
                } else {
                    $message .= 'Previously attempted but not completed.</p>';
                }
            } else {
                $message .= '<p><strong>Previous Performance:</strong> No previous attempt.</p>';
            }

            $message .= '<p><strong>Recommendation:</strong> ';
            if (count($matchingPLOs) >= 2) {
                $message .= 'This course is recommended as it covers multiple failed PLOs.</p>';
            } else {
                $message .= 'This course may help with one failed PLO, but consider other options for comprehensive improvement.</p>';
            }
            $message .= '</div>';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing CRR: ' . $e->getMessage()
            ]);
        }
    }

    public function updateRecommendedCourse(Request $request)
    {
        try {
            $ploId = $request->plo_id;
            $courseId = $request->course_id;

            $failPLO = failPLO::find($ploId);
            if (!$failPLO) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed PLO record not found.'
                ]);
            }

            $course = Course::find($courseId);
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected course not found.'
                ]);
            }

            $failPLO->update([
                'nextOfferplosCource' => $course->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Recommended course updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating recommended course: ' . $e->getMessage()
            ]);
        }
    }

    public function getFailedPlos($courseId, $studentId)
    {
        try {
            // Get the student's PLO data
            $failedPlos = failPLO::where('course_id', $courseId)
                ->where('student_id', $studentId)
                ->latest()
                ->first();

            if (!$failedPlos) {
                return response()->json([
                    'success' => false,
                    'message' => 'No failed PLOs found'
                ]);
            }

            return response()->json([
                'success' => true,
                'failedPlos' => json_decode($failedPlos->failPLOs, true)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving failed PLOs: ' . $e->getMessage()
            ]);
        }
    }

    public function getNextSemesterCourses($courseId)
    {
        try {
            $currentCourse = Course::findOrFail($courseId);
            $nextSemester = $currentCourse->semester + 1;

            // Get courses from the next semester
            $nextSemesterCourses = Course::where('semester', $nextSemester)
                ->get(['id', 'name', 'code']);

            return response()->json([
                'success' => true,
                'courses' => $nextSemesterCourses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving next semester courses: ' . $e->getMessage()
            ]);
        }
    }

    public function sendCourseRecommendation(Request $request)
    {
        try {
            $validated = $request->validate([
                'course_id' => 'required|exists:courses,id',
                'student_id' => 'required|exists:users,id'
            ]);

            // Create or update course registration with pending status
            $registration = CourseRegistration::updateOrCreate(
                [
                    'student_id' => $validated['student_id'],
                    'course_id' => $validated['course_id']
                ],
                [
                    'status' => 'pending',
                    'is_recommended' => true
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Course recommendation sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending course recommendation: ' . $e->getMessage()
            ]);
        }
    }

    public function plo_counseling_result(Request $request)
    {
        $student_id = $request->student_id;
        $course_id = $request->course_id;

        // Get the course registration
        $courseRegistration = CourseRegistration::where('student_id', $student_id)
            ->where('course_id', $course_id)
            ->first();

        if (!$courseRegistration) {
            return redirect()->back()->with('error', 'Course registration not found.');
        }

        // Get failed PLOs for this course
        $failedPLOs = failPLO::with(['course', 'student', 'faculty'])
            ->where('student_id', $student_id)
            ->where('course_id', $course_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get next semester courses for recommendations
        $course = Course::find($course_id);
        $nextSemesterCourses = Course::where('semester', $course->semester + 1)->get();

        return view('lecturar.advisor.plo_counseling_result', compact(
            'courseRegistration',
            'failedPLOs',
            'nextSemesterCourses'
        ));
    }

    public function sendRecommendation(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'plo' => 'required|string',
            'recommended_course_id' => 'required|integer',
        ]);

        // Find the failPLO record for this student and PLO
        $failPLO = failPLO::where('student_id', $request->student_id)
            ->whereJsonContains('failPLOs', [$request->plo])
            ->latest()
            ->first();

        if ($failPLO) {
            $failPLO->recommendation_status = 'pending';
            $failPLO->recommended_course_id = $request->recommended_course_id;
            $failPLO->save();
        }

        return back()->with('success', 'Recommendation sent!');
    }

}

