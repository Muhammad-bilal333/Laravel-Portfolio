<?php

namespace App\Models;
use \App\Models\Faculty;
use App\Models\Role;
use \App\Models\Stu;
use \App\Models\Course;

use Illuminate\Database\Eloquent\Model;

class LabAssessmentRubricDetail extends Model
{
    
    protected $table = 'lab_assessment_rubric_details';

    protected $fillable = ['lab_assessment_id', 'rubric_number','clo_number', 'total_marks', 'obtained_marks'];


    public function assessment() {
        return $this->belongsTo(LabAssessment::class, 'lab_assessment_id');
    }

    public function lab_assessment()
    {
        return $this->belongsTo(LabAssessment::class, 'lab_assessment_id');
    }
}
