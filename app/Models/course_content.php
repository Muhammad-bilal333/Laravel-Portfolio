<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class course_content extends Model
{
    public function courseAllocations()
    {
        return $this->hasMany(CourseAllocation::class, 'course_id');
    }

    public function faculties()
    {
        return $this->hasManyThrough(Faculty::class, CourseAllocation::class, 'course_id', 'id', 'id', 'faculty_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
    public function courseRegistrations()
    {
        return $this->hasMany(CourseRegistration::class, 'course_id');
    }

    public function course_content_point()
    {
        return $this->hasMany(course_content_point::class, 'course_id');
    }

    public function course_content()
    {
        return $this->hasMany(course_content::class, 'course_id');
    }

    public function course_outcome()
    {
        return $this->hasMany(course_outcome::class, 'course_id');
    }

    public function course_detail()
    {
        return $this->hasMany(course_detail::class, 'course_id');
    }
    public function practical_outcome()
    {
        return $this->hasMany(practical_outcome::class, 'course_id');
    }

    protected $fillable = [
        'name', 
        'code', 
        'semester', 
        'batch', 
        'section', 
        'created_by',
        'heading_number',
        'heading_title',
        'course_id'
    ];

}