<?php
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\FacultyMiddleware;
use App\Http\Middleware\StudentMiddleware;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Faculty\FacultyDashboardController;
use App\Http\Controllers\Faculty\CourseController;
use App\Http\Controllers\Faculty\CourseAllocationController;
use App\Http\Controllers\Student\CourseRegistrationController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\QECController;

use App\Http\Controllers\ExportController;
use GuzzleHttp\Middleware;

// Route::get('/', function () {
//         return view('welcome');
//     })->name('home');
    Route::get('/', [LoginController::class, 'showhomepage'])->name('home');
//     Route::get('/', [LoginController::class, 'apifunction'])->name('api');   

//Authentication Routes
// Admin Authentication Routes
        Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('showAdminLoginForm');
        Route::post('/admin/authenticate', [LoginController::class, 'adminAuthenticate'])->name('admin.authenticate');
        Route::middleware([AdminMiddleware::class])->group(function () {
                Route::get('/admin/dashboard', [LoginController::class, 'adminDashboard'])->name('admin.dashboard');
            });

        Route::post('/admin/logout', [LogoutController::class, 'adminlogout'])->name('admin.logout');
        Route::get('/Qualityenhancementcell/dashboard', [LoginController::class, 'QualityenhancementcellDashboard'])->name('Qualityenhancementcell.dashboard');
        
        // faculty Authentication Routes
        Route::get('/faculty/login', [LoginController::class, 'showFacultyLoginForm'])->name('showFacultyLoginForm');
        Route::post('/faculty/authenticate', [LoginController::class, 'facultyAuthenticate'])->name('faculty.authenticate');
        Route::middleware([FacultyMiddleware::class])->group(function () {
                Route::get('/lecturar/dashboard', [FacultyDashboardController::class, 'lecturarDashboard'])->name('lecturar.dashboard');
        });
        Route::post('/faculty/logout', [LogoutController::class, 'facultylogout'])->name('faculty.logout');
        
        // student Authentication Routes
        Route::get('/login',  [LoginController::class, 'showStudentLoginForm'])->name('showStudentLoginForm');
        Route::post('/student/authenticate', [LoginController::class, 'studentAuthenticate'])->name('student.authenticate');

        Route::middleware([StudentMiddleware::class])->group(function () {
                Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
                 Route::get('/failed/plos', [CourseAllocationController::class, 'failed_plos_result'])->name('marks.failed_plos_result.word');
                 Route::post('/find-crr', [CourseAllocationController::class, 'findCRR'])->name('find.crr');
                 Route::post('/update-recommended-course', [CourseAllocationController::class, 'updateRecommendedCourse'])->name('update.recommended.course');
        });
        Route::post('/student/logout', [LogoutController::class, 'studentlogout'])->name('student.logout');
        
        
//Authentication Routes
Route::middleware([AdminMiddleware::class])->group(function () {
        Route::get('/create/Student', [RegisterController::class, 'ShowaddStudent'])->name('add.student');
        Route::delete('/remove/Student{id}', [RegisterController::class, 'deleteStudent'])->name('delete.student');
        Route::get('/edit/student/{id}', [RegisterController::class, 'editStudent'])->name('edit.student');
        Route::post('/update/student/{id}', [RegisterController::class, 'updateStudent'])->name('update.student');
        Route::get('/Student/list', [RegisterController::class, 'Studentlist'])->name('student.list');
        Route::POST('/register/student', [RegisterController::class, 'registerStudent'])->name('register.student');


        Route::get('/create/faculty', [RegisterController::class, 'ShowaddFaculty'])->name('add.faculty');
        Route::delete('/remove/faculty{id}', [RegisterController::class, 'deletefaculty'])->name('delete.faculty');
        Route::get('/edit/faculty/{id}', [RegisterController::class, 'editfaculty'])->name('edit.faculty');
        Route::post('/update/faculty/{id}', [RegisterController::class, 'updatefaculty'])->name('update.faculty');
        Route::get('/faculty/list', [RegisterController::class, 'facultylist'])->name('faculty.list');
        Route::POST('/register/faculty', [RegisterController::class, 'registerfaculty'])->name('register.faculty');


        Route::get('/create/QualityEnhancementCell', [RegisterController::class, 'ShowaddQualityEnhancementCell'])->name('add.QualityEnhancementCell');
        Route::delete('/remove/QualityEnhancementCell{id}', [RegisterController::class, 'deleteQualityEnhancementCell'])->name('delete.QualityEnhancementCell');
        Route::get('/edit/QualityEnhancementCell/{id}', [RegisterController::class, 'editQualityEnhancementCell'])->name('edit.QualityEnhancementCell');
        Route::post('/update/QualityEnhancementCell/{id}', [RegisterController::class, 'updateQualityEnhancementCell'])->name('update.QualityEnhancementCell');
        Route::get('/QualityEnhancementCell/list', [RegisterController::class, 'QualityEnhancementCelllist'])->name('QualityEnhancementCell.list');
        Route::POST('/register/QualityEnhancementCell', [RegisterController::class, 'registerQualityEnhancementCell'])->name('register.QualityEnhancementCell');
});

// Add this route groupe
        Route::get('/duties/{duty}', [FacultyDashboardController::class, 'showDutyDashboard'])->name('duty.dashboard');

        Route::post('/get/courses', [FacultyDashboardController::class, 'getCoursesBySemesterhod'])->name('get.hod.courses');
        Route::get('/courses/detail/edit/{id}', [FacultyDashboardController::class, 'courselist_detail_hod'])->name('course_detail.edit'); 
        Route::post('/lecturer/course-details/update/{course}', [FacultyDashboardController::class, 'updateCourseDetails'])->name('lecturer.course.update');
       
        Route::get('/courses/detail/add', [FacultyDashboardController::class, 'courselist_detail_add'])->name('course_detail.add'); 
        Route::post('/lecturer/course-details/save', [FacultyDashboardController::class, 'storeCourseDetails'])->name('lecturer.course.save');



        Route::get('/faculty/dashboard', [FacultyDashboardController::class, 'dashboard'])->name('faculty.dashboard');
        Route::get('/faculty/add-course', [FacultyDashboardController::class, 'addCourse'])->name('faculty.add_course');
        Route::get('/faculty/assign-faculty', [FacultyDashboardController::class, 'assignFaculty'])->name('faculty.assign_faculty');
        Route::get('/faculty/course-list', [FacultyDashboardController::class, 'courseList'])->name('faculty.course_list');
        // Add this route in web.php
        



        
        // program manger
        Route::get('/courses', [CourseController::class, 'courselist'])->name('course.list'); 
        Route::get('/courses/detail/{id}', [CourseController::class, 'courselist_detail'])->name('course_detail'); 
        Route::post('/get-courses', [CourseController::class, 'getCoursesBySemester'])->name('get.courses');
        // Route::get('/faculty', [CourseControllfer::class, 'facultylist'])->name('facultypro.list');
         
        Route::get('/faculty', [CourseController::class, 'facultylist'])->name('facultypro.list'); 

        Route::get('/course-allocation', [CourseController::class, 'courseallocate'])->name('course.allocate');
        Route::post('/assign-faculty', [CourseController::class, 'courseAllocateStore'])->name('assign.faculty.store');

        Route::get('/assign/advisor', [CourseController::class, 'Assignadvisor'])->name('Assign.advisor');
        Route::post('/assign/advisor/store', [CourseController::class, 'Assignadvisorstore'])->name('Assign.advisor.store');

        Route::get('/courses/create', [CourseController::class, 'create'])->name('newcourse.create');
        Route::post('/courses/store', [CourseController::class, 'store'])->name('newcourse.store');

    


        //courses register
        Route::post('/register-courses', [CourseRegistrationController::class, 'store'])->name('courses.register');

        

        //courses advisor
        Route::get('/single-student-courses/{id}', [CourseAllocationController::class, 'SingleStuCources'])->name('courses.SingleStuCources');
        Route::post('/update-status/{id}', [CourseAllocationController::class, 'updateStatus'])->name('courses.updateStatus');
        // Route::get('/counseling', [CourseAllocationController::class, 'plo_counseling'])->name('courses.plo_counseling');
        Route::get('/counseling/{id}', [CourseAllocationController::class, 'plo_counseling'])->name('courses.plo_counseling');
        Route::get('/counseling/plo', [CourseController::class, 'getStudentsWithLowPLO'])->name('courses.getStudentsWithLowPLO');



        //Lecturar
        Route::get('/registered-students/{id}', [CourseAllocationController::class, 'AllRegisteredStudent'])->name('courses.AllRegisteredStudent');
        Route::get('/student-marks/{id}', [CourseAllocationController::class, 'student_marks'])->name('student.marks');
        Route::get('/student-marks-lab/{id}', [CourseAllocationController::class, 'student_marks_lab'])->name('student_marks_lab.marks');
        Route::get('/add-student-marks/{id}', [CourseAllocationController::class, 'add_student_marks'])->name('student.add_marks');
        Route::get('/add-student-lab-marks/{id}', [CourseAllocationController::class, 'add_studentlab_marks'])->name('studentlab.add_marks');
        Route::Post('/store-student-marks', [CourseAllocationController::class, 'store_student_marks'])->name('student.store_marks');
        Route::Post('/store-student-marks-lab', [CourseAllocationController::class, 'store_student_marks_lab'])->name('student_lab.store_marks');
        Route::post('/update-course/{id}', [CourseController::class, 'updateCourseDetails'])->name('program_manager.course.update');
        Route::delete('/delete-student-marks/{id}', [CourseAllocationController::class, 'delete_student_marks'])->name('student.delete_marks');

        //session
        Route::get('/set-course/{course_id}', [CourseAllocationController::class, 'setCourseSession'])->name('courses.setCourseSession');

        //session
        // Route::get('/exportexcel', [CourseAllocationController::class, 'labexportToExcel'])->name('marks.export');
        Route::get('/exportexcel', [CourseAllocationController::class, 'exportToExcel'])->name('marks.export');
        Route::get('/exportexcel_lab', [CourseAllocationController::class, 'exportToExcel_lab'])->name('marks.exportlab');
        Route::get('/exportword', [CourseAllocationController::class, 'exportToWord'])->name('marks.export.word');
        Route::get('/exportwordlab', [CourseAllocationController::class, 'exportToWordlab'])->name('marks.exportlab.word');
        Route::get('/plocounselingresult', [CourseAllocationController::class, 'plo_counseling_result'])->name('marks.plo_counseling_result.word');
        Route::get('/plocounselingresults', [CourseAllocationController::class, 'plo__not_lab'])->name('marks.plo_counseling_result_not_lab.word');


       
        
// Route::group(['middleware' => ['auth', 'role:admin']], function () {
//     // admin routes
// });
        // Admin Dashboard   all_registered_student
// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
// });

// // Faculty Dashboard
// Route::middleware(['auth', 'faculty'])->group(function () {
//     Route::get('/faculty/dashboard', [FacultyDashboardController::class, 'index'])->name('faculty.dashboard');
// });

// // Student Dashboard
// Route::middleware(['auth', 'student'])->group(function () {
//     Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
// });

Route::post('/check-email', [App\Http\Controllers\Auth\RegisterController::class, 'checkEmail'])->name('check.email');

Route::get('/qec/login', [LoginController::class, 'showQecLoginForm'])->name('showQecLoginForm');
Route::post('/qec/authenticate', [LoginController::class, 'qecAuthenticate'])->name('qec.authenticate');
Route::post('/qec/logout', [LogoutController::class, 'qecLogout'])->name('qec.logout');

// Add QEC routes
Route::middleware(['auth'])->group(function () {
    Route::get('/qec/failed-plos', function() {
        return view('Qualityenhancementcell.simple_failed_plos');
    })->name('qec.failed_plos');
});

Route::get('/hod/plos', [App\Http\Controllers\Faculty\CourseAllocationController::class, 'viewPLOs'])->name('hod.plos');

// QEC Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/qec/dashboard', [QECController::class, 'dashboard'])->name('qec.dashboard');
    Route::get('/qec/failed-plos', [QECController::class, 'failedPLOs'])->name('qec.failed-plos');
    Route::get('/qec/crr-data', [QECController::class, 'fetchCRRData'])->name('qec.crr-data');
    Route::get('/qec/crr/{id}', [QECController::class, 'viewCRR'])->name('qec.view-crr');
    Route::get('/qec/crr/{id}/download', [QECController::class, 'downloadCRR'])->name('qec.download-crr');
    Route::post('/qec/crr/{id}/status', [QECController::class, 'updateCRRStatus'])->name('qec.update-crr-status');
});

// PLO Counseling API Routes
Route::get('/api/failed-plos/{courseId}/{studentId}', [CourseAllocationController::class, 'getFailedPlos'])->name('api.failed-plos');
Route::get('/api/next-semester-courses/{courseId}', [CourseAllocationController::class, 'getNextSemesterCourses'])->name('api.next-semester-courses');
Route::post('/api/send-course-recommendation', [CourseAllocationController::class, 'sendCourseRecommendation'])->name('api.send-recommendation');

Route::post('/advisor/send-recommendation', [CourseAllocationController::class, 'sendRecommendation'])->name('advisor.sendRecommendation');

Route::post('/send-course-recommendation', [CourseAllocationController::class, 'sendCourseRecommendation'])->name('sendCourseRecommendation');
