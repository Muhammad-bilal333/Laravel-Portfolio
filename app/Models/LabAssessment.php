<?php

namespace App\Models;
use \App\Models\Faculty;
use App\Models\Role;
use \App\Models\Stu;
use \App\Models\Course;

use Illuminate\Database\Eloquent\Model;

class LabAssessment  extends Model
{
    
    protected $table = 'lab_assessments';

    // protected $fillable = [
    //     'course_id', 
    //     'teacher_id', 
    //     'student_id', 
    //     'type',
    //     'assessment_title',
    // ]; 

    protected $fillable = ['course_id', 'teacher_id', 'student_id', 'type', 'assessment_title'];

    
    // Define relationships (if applicable)
    public function marks() {
        return $this->hasMany(LabAssessmentRubricDetail::class, 'lab_assessment_id');
    }
    public function student()
    {
        return $this->hasOne(User::class, 'id', 'student_id');
    }
    public function Cource()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }
    public function studentdetail()
    {
        return $this->hasOne(Stu::class, 'user_id', 'student_id');
    }

    
}
