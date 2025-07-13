<?php

namespace App\Models;
use \App\Models\Faculty;
use App\Models\Role;
use \App\Models\Stu;
use \App\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CourseAllocation extends Model
{
    protected $table = 'course_allocations'; // Define the table name if different from Laravel's convention

    protected $fillable = [
        'course_id', 
        'faculty_id', 
        'class', 
        'batch',
        'section',
        'registrants', 
        'classes', 
        'percentage'
    ]; // Specify fillable fields to allow mass assignment

    // Define relationships (if applicable)
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');  // Assuming 'course_id' is the foreign key
    }

    public function registrations()
    {
        return $this->hasMany(CourseRegistration::class, 'course_allocation_id');
    }
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
