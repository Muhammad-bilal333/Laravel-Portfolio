<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');  
    }

    // Relationship with User (Teacher)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');  
    }

    // Relationship with CourseAllocation
    public function courseAllocation()
    {
        return $this->belongsTo(CourseAllocation::class, 'cource_allocation_id');
    }

    // Relationship with Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function studentDetails()
    {
        return $this->hasOneThrough(Stu::class, User::class, 'id', 'user_id', 'student_id', 'id');
    }



    protected $fillable = [
        'student_id',
        'course_id',
        'teacher_id',
        'cource_allocation_id',
        'status',
    ];
}
