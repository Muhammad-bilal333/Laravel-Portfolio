<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Faculty;
use App\Models\Stu;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id'
    ];

    public function faculty()
    {
        return $this->hasOne(Faculty::class);
    }
    public function student()
    {
        return $this->hasOne(Stu::class, 'user_id', 'id'); 
    }

    public function registeredCourses()
    {
        return $this->hasMany(CourseRegistration::class, 'student_id');
    }

    // Teacher relationship with CourseRegistration
    public function taughtCourses()
    {
        return $this->hasMany(CourseRegistration::class, 'teacher_id');
    }

    public function classAssignments()
    {
        return $this->hasMany(AdvisorClassAssignment::class, 'advisor_id');
    }
   

    /**
     * Define relationship with the Student model
     */

     
    // public function student(): HasOne
    // {
    //     return $this->hasOne(Student::class);
    // }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
