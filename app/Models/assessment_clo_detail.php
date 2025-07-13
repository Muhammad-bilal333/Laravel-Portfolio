<?php

namespace App\Models;
use \App\Models\Faculty;
use App\Models\Role;
use \App\Models\Stu;
use \App\Models\Course;

use Illuminate\Database\Eloquent\Model;

class assessment_clo_detail extends Model
{
    

    protected $fillable = [
        'assessment_id', 
        'clo_number', 
        'total_marks', 
        'obtained_marks',
    ]; // Specify fillable fields to allow mass assignment

    public function assessment() {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }
}
