<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\User;
use App\Models\Stu;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    
    // Student CRUD
    public function ShowaddStudent(){
        return view('admin.addStudent');      
    }

    public function registerStudent(Request $request) 
    {
        // Validate the incoming request
        // dd($request->all());
        $request->validate([
            'name'       => 'required|string|max:191',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
            'reg_no'     => 'required|unique:students,roll_number',
            'department' => 'required',
            'section'    => 'nullable|string',
            'batch'      => 'required'
        ]);
    
        // Store the data inside a transaction
        DB::beginTransaction();
    
        try {
            // Create the user entry
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password), // Encrypt password
                'role_id'  => 3, // Assuming '3' is the role_id for students
            ]);
    
            // Create the student entry linked to the user
            Stu::create([
                'user_id'     => $user->id,
                'roll_number' => $request->reg_no,
                'section'     => $request->section ?? null,
                'Batch'       => $request->batch,  // Ensure lowercase to match the table column
                'department'  => $request->department,
            ]);
    
            // Commit the transaction
            DB::commit();
    
            return redirect()->route('student.list')->with('success', 'Student registered successfully!');
        
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to register student.']);
        }
    }
    

    public function Studentlist(Request $request)
{
    $search = $request->input('search');
    
    $students = Stu::with('user')
    ->where('roll_number', 'LIKE', "%$search%")
    ->orWhere('batch', 'LIKE', "%$search%")
    ->orWhere('department', 'LIKE', "%$search%")
    ->orWhereHas('user', function ($query) use ($search) {
        $query->where('name', 'LIKE', "%$search%")
        ->orWhere('email', 'LIKE', "%$search%");
    })
    ->get();
    
    return view('admin.student_list', compact('students'));
}

public function editStudent($id)
{
    $student = Stu::with('user')->find($id);

    if (!$student) {
        return redirect()->route('student.list')->with('error', 'Student not found!');
    }

    return view('admin.editStudent', compact('student'));
}
    public function updateStudent(Request $request, $id)
{
    $request->validate([
        'name'       => 'required|string|max:191',
        'email'      => 'required|email|',
        'reg_no'     => 'required|unique:students,roll_number,' . $id . ',id',
        'batch'      => 'required',
        'department' => 'required'
    ]);

    DB::beginTransaction();

    try {
        // Find student
        $student = Stu::find($id);
        if (!$student) {
            return redirect()->route('student.list')->with('error', 'Student not found!');
        }

        // Update user table
        $user = User::find($student->user_id);
        $user->update([
            'name'  => $request->name,
            'email' => $request->email
        ]);

        // Update student table
        $student->update([
            'roll_number' => $request->reg_no,
            'Batch'       => $request->batch,
            'department'  => $request->department
        ]);

        DB::commit();
        return redirect()->route('student.list')->with('success', 'Student updated successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        return back()->withErrors(['error' => 'Failed to update student.']);
    }
}

    public function deleteStudent($id)
    {
        DB::beginTransaction();
        try {
            $student = Stu::where('id', $id)->first();
            if (!$student) {
                return redirect()->back()->with('error', 'Student not found!');
            }
            $userId = $student->user_id;

            // Delete related records first
            \App\Models\CourseRegistration::where('student_id', $userId)->delete();
            \App\Models\Assessment::where('student_id', $userId)->delete();
            \App\Models\LabAssessment::where('student_id', $userId)->delete();

            // Delete student and user records
            $student->delete();
            User::where('id', $userId)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Student and all related records deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to delete student. Error: ' . $e->getMessage());
        }
    }








    // faculty CRUD


    public function ShowaddFaculty()
    {
        // Get only faculty primary roles (exclude admin/student roles)
        $primaryRoles = Role::where('type', 'primary')
                        ->whereNotIn('name', ['Admin', 'Student', 'Quality Enhancement Cell'])
                        ->get();

        // Get all duty roles
        $dutyRoles = Role::where('type', 'duty')->get();

        return view('admin.addFaculty', [
            'primaryRoles' => $primaryRoles,
            'dutyRoles' => $dutyRoles
        ]);
    }

    // public function registerFaculty(Request $request)
    // {
    //     // Validate Input
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:6',
    //         'department' => 'required|string',
    //         'designation' => 'required|string',
    //         'duties' => 'required|array', // Accept multiple checkboxes
    //     ]);

    //     // Determine role_id based on selected duties
    //     $roleMapping = [
    //         'HOD' => 5,
    //         'Program Manager' => 6,
    //         'Course Advisor' => 7,
    //         'None' => 2
    //     ];

    //     $roleId = 2; // Default to "None"
    //     foreach ($request->duties as $duty) {
    //         if (isset($roleMapping[$duty])) {
    //             $roleId = $roleMapping[$duty];
    //             break; // Assign the highest priority duty
    //         }
    //     }

    //     // Create User
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'role_id' => $roleId
    //     ]);

    //     // Create Faculty Profile
    //     Faculty::create([
    //         'user_id' => $user->id,
    //         'department' => $request->department,
    //         'designation' => $request->designation,
    //         'duties' => json_encode($request->duties)
    //     ]);

    //     return redirect()->back()->with('success', 'Faculty registered successfully!');
    // }


    public function registerFaculty(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'department' => 'required|string',
            'designation' => 'required|numeric', // ID of primary role
            'duties' => 'nullable|array',
        ]);

        // Validate designation is a primary role
        $primaryRole = Role::findOrFail($request->designation);
        if ($primaryRole->type !== 'primary') {
            return back()->withErrors(['designation' => 'Invalid primary role selected']);
        }

        // Create User with primary role
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->designation
        ]);

        // Validate and store duties
        $validDuties = [];
        if ($request->has('duties')) {
            $validDuties = Role::whereIn('id', $request->duties)
                ->where('type', 'duty')
                ->pluck('id')
                ->toArray();
        }

        Faculty::create([
            'user_id' => $user->id,
            'department' => $request->department,
            'designation' => $primaryRole->name, // Store role name instead of ID
            'duties' => json_encode($validDuties)
        ]);

        return redirect()->route('faculty.list')->with('success', 'Faculty registered successfully!');
    }
    public function FacultyList(Request $request) 
    {
        $search = $request->input('search');
        
        $faculties = Faculty::with('user')
            ->where('designation', 'LIKE', "%$search%")
            ->orWhere('department', 'LIKE', "%$search%")
            ->orWhereHas('user', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%");
            })
            ->get();
        // dd($faculties);
        return view('admin.facultyList', compact('faculties'));
    }

    public function editFaculty($id)
    {
        $faculty = Faculty::with('user')->find($id);
        // dd($faculty);
        if (!$faculty) {
            return redirect()->route('faculty.list')->with('error', 'Student not found!');
        }

        return view('admin.editFaculty', compact('faculty'));
    }
    public function updateFaculty(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|',
            'department' => 'required|string',
            'designation' => 'required|string',
            'duties' => 'required|array', // Multiple checkboxes
        ]);

        DB::beginTransaction();

        try {
            // Find faculty record
            $faculty = Faculty::find($id);
            if (!$faculty) {
                return redirect()->route('faculty.list')->with('error', 'Faculty member not found!');
            }

            // Update user table
            $user = User::find($faculty->user_id);
            $user->update([
                'name'  => $request->name,
                'email' => $request->email
            ]);

            // Update faculty table
            $faculty->update([
                'department' => $request->department,
                'designation' => $request->designation,
                'duties' => json_encode($request->duties) // Store duties as JSON
            ]);

            DB::commit();
            return redirect()->route('faculty.list')->with('success', 'Faculty member updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update faculty member.']);
        }
    }
    public function deletefaculty($id)
    {
        DB::beginTransaction();
        
        try {
            $faculty = Faculty::where('id', $id)->first();

            if (!$faculty) {
                return redirect()->back()->with('error', 'Faculty not found!');
            }

            $userId = $faculty->user_id;

            // Delete related records first
            \App\Models\CourseRegistration::where('teacher_id', $userId)->delete();
            \App\Models\Assessment::where('teacher_id', $userId)->delete();
            \App\Models\CourseAllocation::where('faculty_id', $userId)->delete();
            \App\Models\AdvisorClassAssignment::where('advisor_id', $userId)->delete();
            \App\Models\LabAssessment::where('teacher_id', $userId)->delete();

            // Delete faculty and user records
            $faculty->delete();
            \App\Models\User::where('id', $userId)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Faculty deleted successfully!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to delete faculty. Error: ' . $e->getMessage());
        }
    }





     // Quality Enhancement Cell CRUD
    public function ShowaddQualityEnhancementCell(){
        return view('admin.addQualityEnhancementCell');      
    }

    public function registerQualityEnhancementCell(Request $request)
    {
        // Validate Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 4
        ]);

        // Create Faculty Profile
        Admin::create([
            'user_id' => $user->id,
        ]);

        return redirect()->route('QualityEnhancementCell.list')->with('success', 'Quality Enhancement Cell registered successfully!');
    }

    public function QualityEnhancementCelllist(Request $request) 
    {
        $search = $request->input('search');
        
        $qualityenhancementcells = User::where('role_id', 4)
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->get();
            
        return view('admin.qualityenhancementcellList', compact('qualityenhancementcells'));
    }

    public function editQualityEnhancementCell($id)
    {
        $QualityEnhancementCell = User::find($id);

        if (!$QualityEnhancementCell) {
            return redirect()->route('QualityEnhancementCell.list')->with('error', 'Student not found!');
        }
        // dd($QualityEnhancementCell);
        return view('admin.editQualityEnhancementCell', compact('QualityEnhancementCell'));
    }


    public function updateQualityEnhancementCell(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:191',
            'email'      => 'required|email|',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Find student
            $QualityEnhancementCell = User::find($id);
            if (!$QualityEnhancementCell) {
                return redirect()->route('QualityEnhancementCell.list')->with('error', 'QualityEnhancementCell not found!');
            }
    
            // Update user table
            $QualityEnhancementCell->update([
                'name'  => $request->name,
                'email' => $request->email
            ]);
    
    
            DB::commit();
            return redirect()->route('QualityEnhancementCell.list')->with('success', 'QualityEnhancementCell updated successfully!');
    
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update student.']);
        }
    }
    public function deleteQualityEnhancementCell($id)
    {
        DB::beginTransaction();
        try {
            $qec = User::where('id', $id)->first();
            if (!$qec) {
                return redirect()->back()->with('error', 'Quality Enhancement Cell member not found!');
            }

            // Delete related records first
            \App\Models\Admin::where('user_id', $id)->delete();

            // Delete user record
            $qec->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Quality Enhancement Cell member and all related records deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to delete Quality Enhancement Cell member. Error: ' . $e->getMessage());
        }
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $exists = \App\Models\User::where('email', $email)->exists();
        
        return response()->json(['exists' => $exists]);
    }
}
