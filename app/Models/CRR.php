<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CRR extends Model
{
    protected $fillable = [
        'course_code',
        'course_name',
        'lecturer_id',
        'total_students',
        'failed_plos',
        'status',
        'last_updated',
        'file_path',
        'qec_comments'
    ];

    protected $casts = [
        'last_updated' => 'datetime',
        'failed_plos' => 'array'
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_code', 'code');
    }
} 