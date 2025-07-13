<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class failPLO extends Model
{
    protected $table = 'failplo';
  protected $fillable = [
    'course_id',
    'student_id',
    'faculty_id',
    'failPLOs',
    'nextOfferplosCource',
];

  // Relationship with Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Relationship with Student (assuming Student model is User)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship with Faculty (assuming Faculty model is User too)
    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

}